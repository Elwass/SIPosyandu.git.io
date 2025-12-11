<?php
class PatientChild extends BaseModel
{
    public function listWithResidents(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*
             FROM patient_children pc
             JOIN residents r ON r.id = pc.resident_id
             WHERE pc.user_id = :user_id
             ORDER BY r.name'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function attach(int $userId, int $residentId): void
    {
        $stmt = $this->db->prepare('INSERT IGNORE INTO patient_children (user_id, resident_id) VALUES (:user_id, :resident_id)');
        $stmt->execute([
            'user_id' => $userId,
            'resident_id' => $residentId,
        ]);
    }
}
