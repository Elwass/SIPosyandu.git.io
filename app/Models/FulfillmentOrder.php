<?php

class FulfillmentOrder extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO fulfillment_orders (recommendation_id, resident_id, fulfillment_method, address, delivery_fee, total_amount, payment_status, midtrans_order_id, snap_token) VALUES (:recommendation_id, :resident_id, :fulfillment_method, :address, :delivery_fee, :total_amount, :payment_status, :midtrans_order_id, :snap_token)');
        $stmt->execute([
            'recommendation_id' => $data['recommendation_id'],
            'resident_id' => $data['resident_id'],
            'fulfillment_method' => $data['fulfillment_method'],
            'address' => $data['address'],
            'delivery_fee' => $data['delivery_fee'],
            'total_amount' => $data['total_amount'],
            'payment_status' => $data['payment_status'] ?? 'UNPAID',
            'midtrans_order_id' => $data['midtrans_order_id'] ?? null,
            'snap_token' => $data['snap_token'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findWithRecommendation(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT fo.*, r.resident_id, r.notes, r.status AS recommendation_status FROM fulfillment_orders fo JOIN recommendations r ON r.id = fo.recommendation_id WHERE fo.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $order = $stmt->fetch();
        return $order ?: null;
    }

    public function findByMidtransOrderId(string $midtransOrderId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM fulfillment_orders WHERE midtrans_order_id = :midtrans_order_id LIMIT 1');
        $stmt->execute(['midtrans_order_id' => $midtransOrderId]);
        $order = $stmt->fetch();
        return $order ?: null;
    }

    public function findDetailed(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT fo.*, r.notes, r.status AS recommendation_status, res.name AS resident_name, res.nik, res.category, res.phone, res.address FROM fulfillment_orders fo JOIN recommendations r ON r.id = fo.recommendation_id JOIN residents res ON res.id = fo.resident_id WHERE fo.id = :id LIMIT 1');
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

    public function updatePaymentData(int $id, ?string $midtransOrderId, ?string $snapToken): void
    {
        $stmt = $this->db->prepare('UPDATE fulfillment_orders SET midtrans_order_id = :midtrans_order_id, snap_token = :snap_token WHERE id = :id');
        $stmt->execute([
            'midtrans_order_id' => $midtransOrderId,
            'snap_token' => $snapToken,
            'id' => $id,
        ]);
    }

    public function listByResident(int $residentId): array
    {
        $stmt = $this->db->prepare('SELECT fo.*, r.notes, r.status AS recommendation_status FROM fulfillment_orders fo JOIN recommendations r ON r.id = fo.recommendation_id WHERE fo.resident_id = :resident_id ORDER BY fo.created_at DESC');
        $stmt->execute(['resident_id' => $residentId]);
        return $stmt->fetchAll();
    }

    public function listAll(): array
    {
        $query = 'SELECT fo.*, r.notes, r.status AS recommendation_status, res.name AS resident_name, res.nik, res.phone FROM fulfillment_orders fo JOIN recommendations r ON r.id = fo.recommendation_id JOIN residents res ON res.id = fo.resident_id ORDER BY fo.created_at DESC';
        return $this->db->query($query)->fetchAll();
    }

    public function latestForRecommendation(int $recommendationId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM fulfillment_orders WHERE recommendation_id = :id ORDER BY created_at DESC, id DESC LIMIT 1');
        $stmt->execute(['id' => $recommendationId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
