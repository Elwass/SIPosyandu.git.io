<?php

class Recommendation extends BaseModel
{
    public function forPatient(int $patientId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM recommendations WHERE patient_id = :patient ORDER BY created_at DESC');
        $stmt->execute(['patient' => $patientId]);
        return $stmt->fetchAll();
    }

    public function findWithItems(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT r.*, u.name AS patient_name FROM recommendations r JOIN users u ON u.id = r.patient_id WHERE r.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $header = $stmt->fetch();
        if (!$header) {
            return null;
        }

        $itemsStmt = $this->db->prepare('SELECT ri.*, m.name AS medicine_name, m.unit, m.price FROM recommendation_items ri JOIN medicines m ON m.id = ri.medicine_id WHERE ri.recommendation_id = :id');
        $itemsStmt->execute(['id' => $id]);
        $header['items'] = $itemsStmt->fetchAll();

        return $header;
    }

    public function create(array $data, array $items): int
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare('INSERT INTO recommendations (patient_id, admin_id, notes, status) VALUES (:patient_id, :admin_id, :notes, :status)');
        $stmt->execute([
            'patient_id' => $data['patient_id'],
            'admin_id' => $data['admin_id'],
            'notes' => $data['notes'],
            'status' => $data['status'] ?? 'SENT',
        ]);

        $recommendationId = (int) $this->db->lastInsertId();

        $itemStmt = $this->db->prepare('INSERT INTO recommendation_items (recommendation_id, medicine_id, qty, dosage, note) VALUES (:recommendation_id, :medicine_id, :qty, :dosage, :note)');
        foreach ($items as $item) {
            $itemStmt->execute([
                'recommendation_id' => $recommendationId,
                'medicine_id' => $item['medicine_id'],
                'qty' => $item['qty'],
                'dosage' => $item['dosage'],
                'note' => $item['note'],
            ]);
        }

        $this->db->commit();
        return $recommendationId;
    }
}
