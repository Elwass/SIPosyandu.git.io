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

    public function upcomingForResidents(array $residentIds): array
    {
        if (empty($residentIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($residentIds), '?'));
        $stmt = $this->db->prepare(
            "SELECT i.*, r.name AS resident_name
             FROM immunizations i
             JOIN residents r ON r.id = i.resident_id
             WHERE i.status = 'scheduled' AND i.schedule_date >= CURDATE() AND i.resident_id IN ($placeholders)
             ORDER BY i.schedule_date ASC"
        );
        $stmt->execute($residentIds);
        return $stmt->fetchAll();
    }
}
