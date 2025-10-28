<?php
class Measurement extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->db->query('SELECT m.*, r.name AS resident_name, r.category FROM measurements m JOIN residents r ON r.id = m.resident_id ORDER BY m.measured_at DESC');
        return $stmt->fetchAll();
    }

    public function findByResident(int $residentId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM measurements WHERE resident_id = :id ORDER BY measured_at DESC');
        $stmt->execute(['id' => $residentId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO measurements (resident_id, weight, height, muac, nutritional_status, notes, measured_at) VALUES (:resident_id, :weight, :height, :muac, :nutritional_status, :notes, :measured_at)');
        $stmt->execute($data);
    }
}
