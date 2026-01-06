<?php

class Recommendation extends BaseModel
{
    public function forResidents(array $residentIds): array
    {
        if (!$residentIds) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($residentIds), '?'));
        $stmt = $this->db->prepare("SELECT * FROM recommendations WHERE resident_id IN ($placeholders) ORDER BY created_at DESC");
        $stmt->execute($residentIds);
        return $stmt->fetchAll();
    }

    public function findWithItems(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT r.*, res.name AS resident_name, res.nik, res.category, res.phone FROM recommendations r JOIN residents res ON res.id = r.resident_id WHERE r.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $header = $stmt->fetch();
        if (!$header) {
            return null;
        }

        $itemsStmt = $this->db->prepare('SELECT ri.*, m.name AS medicine_name, m.unit, m.price, m.image FROM recommendation_items ri JOIN medicines m ON m.id = ri.medicine_id WHERE ri.recommendation_id = :id');
        $itemsStmt->execute(['id' => $id]);
        $header['items'] = $itemsStmt->fetchAll();

        return $header;
    }

    public function create(array $data, array $items): int
    {
        $this->db->beginTransaction();
        $stmt = $this->db->prepare('INSERT INTO recommendations (resident_id, admin_id, notes, status) VALUES (:resident_id, :admin_id, :notes, :status)');
        $stmt->execute([
            'resident_id' => $data['resident_id'],
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
