<?php
class Reminder extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT rm.*, r.name AS resident_name, r.phone, i.vaccine_name FROM reminders rm JOIN residents r ON r.id = rm.resident_id LEFT JOIN immunizations i ON i.id = rm.immunization_id ORDER BY rm.schedule_date ASC');
        return $stmt->fetchAll();
    }

    public function create(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO reminders (resident_id, immunization_id, schedule_date, channel, status) VALUES (:resident_id, :immunization_id, :schedule_date, :channel, :status)');
        $stmt->execute($data);
    }

    public function markSent(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE reminders SET status="sent", sent_at=NOW() WHERE id=:id');
        $stmt->execute(['id' => $id]);
    }
}
