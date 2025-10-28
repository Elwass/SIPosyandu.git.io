<?php
class Analytics extends BaseModel
{
    public function residentCounts(): array
    {
        $stmt = $this->db->query('SELECT category, COUNT(*) as total FROM residents GROUP BY category');
        $result = ['pregnant' => 0, 'toddler' => 0, 'elderly' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['category']] = (int)$row['total'];
        }
        return $result;
    }

    public function measurementSummary(): array
    {
        $stmt = $this->db->query('SELECT nutritional_status, COUNT(*) as total FROM measurements GROUP BY nutritional_status');
        $summary = [];
        foreach ($stmt->fetchAll() as $row) {
            $summary[$row['nutritional_status']] = (int)$row['total'];
        }
        return $summary;
    }

    public function recentMeasurements(int $limit = 5): array
    {
        $stmt = $this->db->prepare('SELECT m.*, r.name AS resident_name FROM measurements m JOIN residents r ON r.id = m.resident_id ORDER BY m.measured_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
