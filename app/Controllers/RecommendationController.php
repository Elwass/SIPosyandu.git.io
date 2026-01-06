<?php

class RecommendationController
{
    private Recommendation $recommendations;
    private Medicine $medicines;

    public function __construct()
    {
        $this->recommendations = new Recommendation();
        $this->medicines = new Medicine();
    }

    public function patientList(): void
    {
        require_role(['pasien']);
        $user = user();
        $residentIds = $this->residentIdsForUser((int) $user['id']);
        $recs = $this->recommendations->forResidents($residentIds);
        include __DIR__ . '/../Views/patient/recommendations.php';
    }

    public function patientDetail(): void
    {
        require_role(['pasien', 'super_admin', 'admin']);
        $id = (int) ($_GET['id'] ?? 0);
        $recommendation = $this->recommendations->findWithItems($id);
        include __DIR__ . '/../Views/patient/recommendation_detail.php';
    }

    public function adminIndex(): void
    {
        require_role(['super_admin', 'admin']);
        $db = Database::getInstance();
        $list = $db->query('SELECT r.*, res.name AS resident_name, res.nik, res.category, res.phone FROM recommendations r JOIN residents res ON res.id = r.resident_id ORDER BY r.created_at DESC')->fetchAll();
        include __DIR__ . '/../Views/admin/recommendations.php';
    }

    public function createForm(): void
    {
        require_role(['super_admin', 'admin']);
        $db = Database::getInstance();
        $patients = $db->query('SELECT id, name, nik, category FROM residents ORDER BY name')->fetchAll();
        $medicines = $this->medicines->active();
        include __DIR__ . '/../Views/admin/recommendation_form.php';
    }

    public function store(): void
    {
        require_role(['super_admin', 'admin']);
        $admin = user();
        $residentId = (int) ($_POST['resident_id'] ?? 0);
        $notes = $_POST['notes'] ?? '';
        $items = $_POST['items'] ?? [];
        $preparedItems = [];
        foreach ($items as $item) {
            if (empty($item['medicine_id']) || empty($item['qty']) || empty($item['dosage'])) {
                continue;
            }
            $preparedItems[] = [
                'medicine_id' => (int) $item['medicine_id'],
                'qty' => (int) $item['qty'],
                'dosage' => $item['dosage'],
                'note' => $item['note'] ?? '',
            ];
        }

        if ($residentId && $preparedItems) {
            $id = $this->recommendations->create([
                'resident_id' => $residentId,
                'admin_id' => $admin['id'],
                'notes' => $notes,
                'status' => 'SENT',
            ], $preparedItems);
            redirect('?page=recommendation-detail&id=' . $id);
            return;
        }

        redirect('?page=admin-recommendations-create');
    }

    private function residentIdsForUser(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT resident_id FROM patient_children WHERE user_id = :user LIMIT 1');
        $stmt->execute(['user' => $userId]);
        $row = $stmt->fetch();
        return $row ? [(int) $row['resident_id']] : [];
    }
}
