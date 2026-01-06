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
        $recs = $this->recommendations->forPatient($user['id']);
        include __DIR__ . '/../Views/patient/recommendations.php';
    }

    public function patientDetail(): void
    {
        require_role(['pasien']);
        $id = (int) ($_GET['id'] ?? 0);
        $recommendation = $this->recommendations->findWithItems($id);
        include __DIR__ . '/../Views/patient/recommendation_detail.php';
    }

    public function adminIndex(): void
    {
        require_role(['super_admin', 'admin']);
        $db = Database::getInstance();
        $list = $db->query('SELECT r.*, u.name AS patient_name FROM recommendations r JOIN users u ON u.id = r.patient_id ORDER BY r.created_at DESC')->fetchAll();
        include __DIR__ . '/../Views/admin/recommendations.php';
    }

    public function createForm(): void
    {
        require_role(['super_admin', 'admin']);
        $db = Database::getInstance();
        $patients = $db->query("SELECT id, name FROM users WHERE role = 'pasien' ORDER BY name")->fetchAll();
        $medicines = $this->medicines->active();
        include __DIR__ . '/../Views/admin/recommendation_form.php';
    }

    public function store(): void
    {
        require_role(['super_admin', 'admin']);
        $admin = user();
        $patientId = (int) ($_POST['patient_id'] ?? 0);
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

        if ($patientId && $preparedItems) {
            $id = $this->recommendations->create([
                'patient_id' => $patientId,
                'admin_id' => $admin['id'],
                'notes' => $notes,
                'status' => 'SENT',
            ], $preparedItems);
            redirect('?page=recommendation-detail&id=' . $id);
            return;
        }

        redirect('?page=admin-recommendations-create');
    }
}
