<?php
class Order extends BaseModel
{
    public function create(array $orderData, array $items): int
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO orders (user_id, pickup_method, address, total_amount, payment_status, fulfillment_status, snap_token, snap_redirect_url) VALUES (:user_id, :pickup_method, :address, :total_amount, :payment_status, :fulfillment_status, :snap_token, :snap_redirect_url)'
            );
            $stmt->execute($orderData);
            $orderId = (int)$this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (:order_id, :product_id, :quantity, :price, :subtotal)'
            );
            foreach ($items as $item) {
                $item['order_id'] = $orderId;
                $itemStmt->execute($item);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateSnapData(int $orderId, string $token, ?string $redirectUrl): void
    {
        $stmt = $this->db->prepare('UPDATE orders SET snap_token = :snap_token, snap_redirect_url = :snap_redirect_url WHERE id = :id');
        $stmt->execute([
            'snap_token' => $token,
            'snap_redirect_url' => $redirectUrl,
            'id' => $orderId,
        ]);
    }

    public function find(int $orderId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = :id');
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch();
        return $order ?: null;
    }

    public function findByUser(int $orderId, int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $orderId, 'user_id' => $userId]);
        $order = $stmt->fetch();
        return $order ?: null;
    }

    public function items(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT oi.*, p.name AS product_name, p.image_url FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE order_id = :order_id');
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function listByUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function listAll(?string $paymentStatus = null): array
    {
        if ($paymentStatus) {
            $stmt = $this->db->prepare('SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON u.id = o.user_id WHERE o.payment_status = :status ORDER BY o.created_at DESC');
            $stmt->execute(['status' => $paymentStatus]);
            return $stmt->fetchAll();
        }

        $stmt = $this->db->query('SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC');
        return $stmt->fetchAll();
    }

    public function updateFulfillment(int $orderId, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE orders SET fulfillment_status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $orderId]);
    }

    public function updatePaymentStatusWithStock(int $orderId, string $paymentStatus): void
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $orderId]);
            $order = $stmt->fetch();
            if (!$order) {
                $this->db->rollBack();
                return;
            }

            $needsStock = $paymentStatus === 'PAID' && (int)$order['stock_deducted'] === 0;

            $update = $this->db->prepare('UPDATE orders SET payment_status = :payment_status WHERE id = :id');
            $update->execute(['payment_status' => $paymentStatus, 'id' => $orderId]);

            if ($needsStock) {
                $items = $this->items($orderId);
                foreach ($items as $item) {
                    $productUpdate = $this->db->prepare('UPDATE products SET stock = GREATEST(stock - :qty, 0) WHERE id = :id');
                    $productUpdate->execute(['qty' => $item['quantity'], 'id' => $item['product_id']]);
                }
                $this->db->prepare('UPDATE orders SET stock_deducted = 1 WHERE id = :id')->execute(['id' => $orderId]);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function reportTotals(?string $from, ?string $to): array
    {
        $conditions = ['payment_status = "PAID"'];
        $params = [];
        if ($from) {
            $conditions[] = 'DATE(created_at) >= :from';
            $params['from'] = $from;
        }
        if ($to) {
            $conditions[] = 'DATE(created_at) <= :to';
            $params['to'] = $to;
        }
        $where = implode(' AND ', $conditions);
        $stmt = $this->db->prepare("SELECT SUM(total_amount) AS total_amount, COUNT(*) AS total_orders FROM orders WHERE $where");
        $stmt->execute($params);
        return $stmt->fetch() ?: ['total_amount' => 0, 'total_orders' => 0];
    }

    public function reportTopProducts(?string $from, ?string $to): array
    {
        $conditions = ['o.payment_status = "PAID"'];
        $params = [];
        if ($from) {
            $conditions[] = 'DATE(o.created_at) >= :from';
            $params['from'] = $from;
        }
        if ($to) {
            $conditions[] = 'DATE(o.created_at) <= :to';
            $params['to'] = $to;
        }
        $where = implode(' AND ', $conditions);
        $sql = "SELECT p.name, SUM(oi.quantity) AS total_qty, SUM(oi.subtotal) AS total_sales
                FROM order_items oi
                JOIN orders o ON o.id = oi.order_id
                JOIN products p ON p.id = oi.product_id
                WHERE $where
                GROUP BY p.id
                ORDER BY total_qty DESC
                LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
