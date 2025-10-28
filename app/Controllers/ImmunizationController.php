<?php
class ImmunizationController
{
    private Immunization $immunizations;
    private Resident $residents;

    public function __construct()
    {
        $this->immunizations = new Immunization();
        $this->residents = new Resident();
    }

    public function index(): void
    {
        require_role(['super_admin', 'admin', 'midwife', 'kader']);
        $immunizations = $this->immunizations->all();
        $residents = $this->residents->all();
        include __DIR__ . '/../Views/immunizations/index.php';
    }

    public function store(): void
    {
        require_role(['super_admin', 'admin', 'midwife']);
        $data = [
            'resident_id' => $_POST['resident_id'],
            'vaccine_name' => $_POST['vaccine_name'],
            'schedule_date' => $_POST['schedule_date'],
            'administered_date' => !empty($_POST['administered_date']) ? $_POST['administered_date'] : null,
            'status' => $_POST['status'],
            'notes' => isset($_POST['notes']) && $_POST['notes'] !== '' ? $_POST['notes'] : null,
        ];
        $this->immunizations->create($data);
        flash('success', 'Jadwal imunisasi tersimpan.');
        redirect('?page=immunizations');
    }

    public function markCompleted(): void
    {
        require_role(['super_admin', 'admin', 'midwife']);
        $id = (int)($_GET['id'] ?? 0);
        $date = $_GET['date'] ?? date('Y-m-d');
        $this->immunizations->markAdministered($id, $date);
        flash('success', 'Status imunisasi diperbarui.');
        redirect('?page=immunizations');
    }
}
