<?php

class FulfillmentOrder extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO fulfillment_orders (recommendation_id, patient_id, fulfillment_method, address, delivery_fee, total_amount, payment_status, midtrans_order_id) VALUES (:recommendation_id, :patient_id, :fulfillment_method, :address, :delivery_fee, :total_amount, :payment_status, :midtrans_order_id)');
        $stmt->execute([
            'recommendation_id' => $data['recommendation_id'],
            'patient_id' => $data['patient_id'],
            'fulfillment_method' => $data['fulfillment_method'],
            'address' => $data['address'],
            'delivery_fee' => $data['delivery_fee'],
            'total_amount' => $data['total_amount'],
            'payment_status' => $data['payment_status'] ?? 'UNPAID',
            'midtrans_order_id' => $data['midtrans_order_id'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findWithRecommendation(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT fo.*, r.patient_id, r.notes, r.status AS recommendation_status FROM fulfillment_orders fo JOIN recommendations r ON r.id = fo.recommendation_id WHERE fo.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch();
        return $order ?: null;
    }

    public function updateStatus(int $id, string $paymentStatus): void
    {
        $stmt = $this->db->prepare('UPDATE fulfillment_orders SET payment_status = :payment_status WHERE id = :id');
        $stmt->execute(['payment_status' => $paymentStatus, 'id' => $id]);
    }

    public function updateMidtransOrder(int $id, string $midtransOrderId): void
    {
        $stmt = $this->db->prepare('UPDATE fulfillment_orders SET midtrans_order_id = :midtrans_order_id WHERE id = :id');
        $stmt->execute(['midtrans_order_id' => $midtransOrderId, 'id' => $id]);
    }

    public function listByPatient(int $patientId): array
    {
        $stmt = $this->db->prepare('SELECT fo.*, r.notes, r.status AS recommendation_status FROM fulfillment_orders fo JOIN recommendations r ON r.id = fo.recommendation_id WHERE fo.patient_id = :patient_id ORDER BY fo.created_at DESC');
        $stmt->execute(['patient_id' => $patientId]);
        return $stmt->fetchAll();
    }

    public function listAll(): array
    {
        $query = 'SELECT fo.*, r.notes, r.status AS recommendation_status, u.name AS patient_name FROM fulfillment_orders fo JOIN recommendations r ON r.id = fo.recommendation_id JOIN users u ON u.id = fo.patient_id ORDER BY fo.created_at DESC';
        return $this->db->query($query)->fetchAll();
    }
}
