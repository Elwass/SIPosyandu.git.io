<?php

class MedicineController
{
    private Medicine $medicines;

    public function __construct()
    {
        $this->medicines = new Medicine();
    }

    public function adminIndex(): void
    {
        require_role(['super_admin', 'admin']);
        $list = $this->medicines->all();
        $editMedicine = null;
        if (isset($_GET['edit_id'])) {
            $editMedicine = $this->medicines->find((int) $_GET['edit_id']);
        }
        include __DIR__ . '/../Views/admin/medicines.php';
    }

    public function store(): void
    {
        require_role(['super_admin', 'admin']);
        $imagePath = $this->handleImageUpload($_FILES['image'] ?? null);
        $data = [
            'name' => $_POST['name'] ?? '',
            'unit' => $_POST['unit'] ?? '',
            'price' => (int) ($_POST['price'] ?? 0),
            'stock' => (int) ($_POST['stock'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'image' => $imagePath,
        ];
        $this->medicines->create($data);
        redirect('?page=admin-medicines');
    }

    public function update(): void
    {
        require_role(['super_admin', 'admin']);
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === 0) {
            redirect('?page=admin-medicines');
        }
        $current = $this->medicines->find($id);
        $imagePath = $this->handleImageUpload($_FILES['image'] ?? null, $current['image'] ?? null);
        $data = [
            'name' => $_POST['name'] ?? '',
            'unit' => $_POST['unit'] ?? '',
            'price' => (int) ($_POST['price'] ?? 0),
            'stock' => (int) ($_POST['stock'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'image' => $imagePath,
        ];
        $this->medicines->update($id, $data);
        redirect('?page=admin-medicines');
    }

    public function destroy(): void
    {
        require_role(['super_admin', 'admin']);
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === 0) {
            redirect('?page=admin-medicines');
        }

        $current = $this->medicines->find($id);
        if ($current && !empty($current['image'])) {
            $this->deleteImageFile($current['image']);
        }

        $this->medicines->delete($id);
        redirect('?page=admin-medicines');
    }

    private function handleImageUpload(?array $file, ?string $existingPath = null): ?string
    {
        if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $existingPath;
        }

        if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return $existingPath;
        }

        $maxSize = 2 * 1024 * 1024;
        if (($file['size'] ?? 0) > $maxSize) {
            return $existingPath;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            return $existingPath;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/medicines/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
        $filename = $safeName . '_' . uniqid('', true) . '.' . $ext;
        $relativePath = 'uploads/medicines/' . $filename;
        $target = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            if ($existingPath) {
                $this->deleteImageFile($existingPath);
            }
            return $relativePath;
        }

        return $existingPath;
    }

    private function deleteImageFile(string $relativePath): void
    {
        $fullPath = __DIR__ . '/../../public/' . ltrim($relativePath, '/');
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
