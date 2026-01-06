<?php
class Payment extends BaseModel
{
    public function record(int $orderId, array $payload): void
    {
        $stmt = $this->db->prepare('SELECT id FROM payments WHERE midtrans_order_id = :midtrans_order_id LIMIT 1');
        $stmt->execute(['midtrans_order_id' => $payload['order_id']]);
        $existing = $stmt->fetch();

        if ($existing) {
            $update = $this->db->prepare('UPDATE payments SET transaction_id=:transaction_id, status=:status, signature_key=:signature_key, raw_response=:raw_response WHERE id=:id');
            $update->execute([
                'transaction_id' => $payload['transaction_id'],
                'status' => $payload['transaction_status'],
                'signature_key' => $payload['signature_key'] ?? null,
                'raw_response' => json_encode($payload),
                'id' => $existing['id'],
            ]);
            return;
        }

        $insert = $this->db->prepare('INSERT INTO payments (order_id, transaction_id, midtrans_order_id, status, signature_key, raw_response) VALUES (:order_id, :transaction_id, :midtrans_order_id, :status, :signature_key, :raw_response)');
        $insert->execute([
            'order_id' => $orderId,
            'transaction_id' => $payload['transaction_id'],
            'midtrans_order_id' => $payload['order_id'],
            'status' => $payload['transaction_status'],
            'signature_key' => $payload['signature_key'] ?? null,
            'raw_response' => json_encode($payload),
        ]);
    }
}
