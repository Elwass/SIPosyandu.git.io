<?php
class ReminderController
{
    private Reminder $reminders;
    private Immunization $immunizations;
    private Resident $residents;

    public function __construct()
    {
        $this->reminders = new Reminder();
        $this->immunizations = new Immunization();
        $this->residents = new Resident();
    }

    public function index(): void
    {
        require_role(['super_admin', 'admin', 'midwife', 'kader']);
        $reminders = $this->reminders->all();
        $immunizations = $this->immunizations->all();
        $residents = $this->residents->all();
        include __DIR__ . '/../Views/reminders/index.php';
    }

    public function store(): void
    {
        require_role(['super_admin', 'admin', 'midwife']);
        $data = [
            'resident_id' => $_POST['resident_id'],
            'immunization_id' => $_POST['immunization_id'] ?: null,
            'schedule_date' => $_POST['schedule_date'],
            'channel' => $_POST['channel'],
            'status' => 'scheduled',
        ];
        $this->reminders->create($data);
        flash('success', 'Reminder berhasil dibuat.');
        redirect('?page=reminders');
    }

    public function markSent(): void
    {
        require_role(['super_admin', 'admin', 'midwife']);
        $id = (int)($_GET['id'] ?? 0);
        $this->reminders->markSent($id);
        flash('success', 'Reminder ditandai telah terkirim.');
        redirect('?page=reminders');
    }
}
