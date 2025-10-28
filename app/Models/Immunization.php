<?php
class Immunization extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT i.*, r.name AS resident_name, r.category FROM immunizations i JOIN residents r ON r.id = i.resident_id ORDER BY i.schedule_date DESC');
        return $stmt->fetchAll();
    }

    public function upcoming(): array
    {
        $stmt = $this->db->query('SELECT i.*, r.name AS resident_name, r.phone FROM immunizations i JOIN residents r ON r.id = i.resident_id WHERE i.status = "scheduled" AND i.schedule_date >= CURDATE() ORDER BY i.schedule_date ASC');
        return $stmt->fetchAll();
    }

    public function create(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO immunizations (resident_id, vaccine_name, schedule_date, administered_date, status, notes) VALUES (:resident_id, :vaccine_name, :schedule_date, :administered_date, :status, :notes)');
        $stmt->execute($data);
    }

    public function markAdministered(int $id, string $date): void
    {
        $stmt = $this->db->prepare('UPDATE immunizations SET status="completed", administered_date=:date WHERE id=:id');
        $stmt->execute(['date' => $date, 'id' => $id]);
    }
}
