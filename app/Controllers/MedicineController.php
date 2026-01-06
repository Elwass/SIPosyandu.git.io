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
        include __DIR__ . '/../Views/admin/medicines.php';
    }

    public function store(): void
    {
        require_role(['super_admin', 'admin']);
        $data = [
            'name' => $_POST['name'] ?? '',
            'unit' => $_POST['unit'] ?? '',
            'price' => (int) ($_POST['price'] ?? 0),
            'stock' => (int) ($_POST['stock'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
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
        $data = [
            'name' => $_POST['name'] ?? '',
            'unit' => $_POST['unit'] ?? '',
            'price' => (int) ($_POST['price'] ?? 0),
            'stock' => (int) ($_POST['stock'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        $this->medicines->update($id, $data);
        redirect('?page=admin-medicines');
    }
}
