<?php

use Midtrans\Config;
use Midtrans\Snap;

class FulfillmentController
{
    private FulfillmentOrder $fulfillments;

    public function __construct()
    {
        $this->fulfillments = new FulfillmentOrder();
    }

    public function patientIndex(): void
    {
        require_role(['pasien']);
        $user = user();
        $residentId = $this->residentIdForUser((int) $user['id']);
        $orders = $residentId ? $this->fulfillments->listByResident($residentId) : [];
        include __DIR__ . '/../Views/patient/fulfillment_orders.php';
    }

    public function adminIndex(): void
    {
        require_role(['super_admin', 'admin']);
        $orders = $this->fulfillments->listAll();
        include __DIR__ . '/../Views/admin/fulfillment_orders.php';
    }

    public function create(): void
    {
        require_role(['pasien']);
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $recommendationId = (int) ($data['recommendation_id'] ?? 0);
        $method = $data['fulfillment_method'] ?? '';
        $address = $data['address'] ?? null;
        $deliveryFee = $method === 'DELIVERY' ? (int) ($data['delivery_fee'] ?? 0) : 0;

        $recommendationModel = new Recommendation();
        $recommendation = $recommendationModel->findWithItems($recommendationId);
        if (!$recommendation) {
            http_response_code(404);
            echo json_encode(['message' => 'Rekomendasi tidak ditemukan']);
            return;
        }

        $total = 0;
        foreach ($recommendation['items'] as $item) {
            $total += ((int) $item['qty']) * ((int) $item['price']);
        }
        $total += $deliveryFee;

        $id = $this->fulfillments->create([
            'recommendation_id' => $recommendationId,
            'resident_id' => $recommendation['resident_id'],
            'fulfillment_method' => $method,
            'address' => $address,
            'delivery_fee' => $deliveryFee,
            'total_amount' => $total,
            'payment_status' => $method === 'SELF_BUY' ? 'UNPAID' : 'UNPAID',
        ]);

        header('Content-Type: application/json');
        echo json_encode(['order_id' => $id]);
    }

    public function pay(): void
    {
        require_role(['pasien']);
        $id = (int) ($_GET['id'] ?? 0);
        $recommendationModel = new Recommendation();
        $order = $this->fulfillments->findWithRecommendation($id);
        if (!$order) {
            http_response_code(404);
            echo json_encode(['message' => 'Pesanan tidak ditemukan']);
            return;
        }
        $recommendation = $recommendationModel->findWithItems((int) $order['recommendation_id']);
        if (!$recommendation) {
            http_response_code(404);
            echo json_encode(['message' => 'Rekomendasi tidak ditemukan']);
            return;
        }

        $midtransOrderId = 'POSYANDU-MED-' . $id . '-' . time();
        $this->fulfillments->updateMidtransOrder($id, $midtransOrderId);
        $this->fulfillments->updateStatus($id, 'PENDING');

        $appConfig = require __DIR__ . '/../config.php';
        Config::$serverKey = $appConfig['midtrans']['server_key'];
        Config::$isProduction = $appConfig['midtrans']['is_production'];
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $items = [];
        foreach ($recommendation['items'] as $item) {
            $items[] = [
                'id' => 'MED-' . $item['medicine_id'],
                'price' => (int) $item['price'],
                'quantity' => (int) $item['qty'],
                'name' => $item['medicine_name'],
            ];
        }
        if ($order['fulfillment_method'] === 'DELIVERY' && (int) $order['delivery_fee'] > 0) {
            $items[] = [
                'id' => 'DELIVERY',
                'price' => (int) $order['delivery_fee'],
                'quantity' => 1,
                'name' => 'Biaya Pengantaran',
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => (int) $order['total_amount'],
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $recommendation['resident_name'] ?? 'Pasien',
                'email' => '',
                'phone' => $recommendation['phone'] ?? '',
            ],
        ];

        $snap = Snap::getSnapToken($payload);
        header('Content-Type: application/json');
        if ($snap && isset($snap['token'])) {
            echo json_encode(['token' => $snap['token']]);
            return;
        }

        http_response_code(500);
        echo json_encode(['message' => 'Gagal membuat pembayaran']);
    }

    public function checkStatus(): void
    {
        require_role(['pasien']);
        $id = (int) ($_GET['id'] ?? 0);
        $order = $this->fulfillments->findWithRecommendation($id);
        if (!$order || empty($order['midtrans_order_id'])) {
            http_response_code(404);
            echo json_encode(['message' => 'Pesanan tidak ditemukan']);
            return;
        }

        $appConfig = require __DIR__ . '/../config.php';
        $serverKey = $appConfig['midtrans']['server_key'];
        $endpoint = 'https://api.sandbox.midtrans.com/v2/' . $order['midtrans_order_id'] . '/status';
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $serverKey . ':');
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode >= 200 && $statusCode < 300 && $response) {
            $data = json_decode($response, true);
            $midtransStatus = $data['transaction_status'] ?? 'unknown';
            $paymentStatus = 'PENDING';
            if (in_array($midtransStatus, ['settlement', 'capture'], true)) {
                $paymentStatus = 'PAID';
            } elseif (in_array($midtransStatus, ['expire', 'cancel', 'deny'], true)) {
                $paymentStatus = 'FAILED';
            }
            $this->fulfillments->updateStatus($id, $paymentStatus);

            header('Content-Type: application/json');
            echo json_encode([
                'payment_status' => $paymentStatus,
                'midtrans_status' => $midtransStatus,
            ]);
            return;
        }

        http_response_code(500);
        echo json_encode(['message' => 'Gagal memeriksa status pembayaran']);
    }

    public function paymentCreate(): void
    {
        header('Content-Type: application/json');

        if (!is_logged_in()) {
            http_response_code(401);
            echo json_encode(['error' => 'Harus login sebagai pasien']);
            return;
        }

        $user = user();
        if (($user['role'] ?? '') !== 'pasien') {
            http_response_code(403);
            echo json_encode(['error' => 'Hanya pasien yang dapat membuat pembayaran']);
            return;
        }

        try {
            $payload = json_decode(file_get_contents('php://input'), true) ?? [];
            $recommendationId = (int) ($payload['recommendation_id'] ?? 0);
            $method = strtoupper(trim((string) ($payload['fulfillment_method'] ?? '')));
            $fulfillmentId = (int) ($payload['fulfillment_order_id'] ?? 0);
            $address = $method === 'DELIVERY' ? trim((string) ($payload['address'] ?? '')) : null;
            $deliveryFee = $method === 'DELIVERY' ? max(0, (int) ($payload['delivery_fee'] ?? 0)) : 0;

            if (!in_array($method, ['PICKUP', 'DELIVERY', 'SELF_BUY'], true)) {
                http_response_code(400);
                echo json_encode(['error' => 'Permintaan tidak valid']);
                return;
            }

            $residentIds = $this->residentIdsForUser((int) $user['id']);

            $recommendationModel = new Recommendation();
            $order = null;
            if ($fulfillmentId) {
                $order = $this->fulfillments->findWithRecommendation($fulfillmentId);
                if (!$order) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Pesanan tidak ditemukan']);
                    return;
                }
                $recommendationId = $recommendationId ?: (int) $order['recommendation_id'];
                $method = $order['fulfillment_method'];
                $deliveryFee = (int) $order['delivery_fee'];
            }

            if (!$recommendationId) {
                http_response_code(400);
                echo json_encode(['error' => 'Rekomendasi tidak valid']);
                return;
            }

            $recommendation = $recommendationModel->findWithItems($recommendationId);
            if (!$recommendation) {
                http_response_code(404);
                echo json_encode(['error' => 'Rekomendasi tidak ditemukan']);
                return;
            }

            if ($residentIds && !in_array((int) $recommendation['resident_id'], $residentIds, true)) {
                http_response_code(403);
                echo json_encode(['error' => 'Anda tidak berhak mengakses rekomendasi ini']);
                return;
            }

            if ($order && (int) $order['resident_id'] !== (int) $recommendation['resident_id']) {
                http_response_code(403);
                echo json_encode(['error' => 'Pesanan tidak sesuai dengan pasien']);
                return;
            }

            [$items, $subtotal] = $this->buildSnapItems($recommendation);
            $totalAmount = $order ? (int) $order['total_amount'] : ($subtotal + $deliveryFee);

            if ($totalAmount <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Total pembayaran tidak valid']);
                return;
            }

            if ($method === 'SELF_BUY') {
                $orderId = $order['id'] ?? $this->fulfillments->create([
                    'recommendation_id' => $recommendationId,
                    'resident_id' => $recommendation['resident_id'],
                    'fulfillment_method' => $method,
                    'address' => $address,
                    'delivery_fee' => $deliveryFee,
                    'total_amount' => $totalAmount,
                    'payment_status' => 'UNPAID',
                    'midtrans_order_id' => null,
                ]);

                echo json_encode([
                    'token' => null,
                    'order_id' => null,
                    'fulfillment_order_id' => $orderId,
                ]);
                return;
            }

            if ($order && strtoupper($order['payment_status']) === 'PAID') {
                http_response_code(400);
                echo json_encode(['error' => 'Pesanan sudah dibayar']);
                return;
            }

            $appConfig = require __DIR__ . '/../config.php';
            $serverKey = $appConfig['midtrans']['server_key'] ?? '';
            $clientKey = $appConfig['midtrans']['client_key'] ?? '';
            if (!$serverKey || !$clientKey) {
                http_response_code(500);
                echo json_encode(['error' => 'Midtrans key belum dikonfigurasi']);
                return;
            }

            Config::$serverKey = $serverKey;
            Config::$isProduction = (bool) ($appConfig['midtrans']['is_production'] ?? false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            if ($method === 'DELIVERY' && $deliveryFee > 0) {
                $items[] = [
                    'id' => 'DELIVERY',
                    'price' => $deliveryFee,
                    'quantity' => 1,
                    'name' => 'Biaya Pengantaran',
                ];
            }

            $midtransOrderId = $order['midtrans_order_id'] ?? ('POSYANDU-MED-' . $recommendationId . '-' . time());
            $payloadSnap = [
                'transaction_details' => [
                    'order_id' => $midtransOrderId,
                    'gross_amount' => $totalAmount,
                ],
                'item_details' => $items,
                'customer_details' => [
                    'first_name' => $recommendation['resident_name'] ?? 'Pasien',
                    'email' => $user['email'] ?? '',
                    'phone' => $recommendation['phone'] ?? '',
                ],
            ];

            if ($order) {
                $this->fulfillments->updateMidtransOrder($fulfillmentId, $midtransOrderId);
                $this->fulfillments->updateStatus($fulfillmentId, 'PENDING');
                $fulfillmentOrderId = $fulfillmentId;
            } else {
                $fulfillmentOrderId = $this->fulfillments->create([
                    'recommendation_id' => $recommendationId,
                    'resident_id' => $recommendation['resident_id'],
                    'fulfillment_method' => $method,
                    'address' => $address,
                    'delivery_fee' => $deliveryFee,
                    'total_amount' => $totalAmount,
                    'payment_status' => 'PENDING',
                    'midtrans_order_id' => $midtransOrderId,
                ]);
            }

            $snapResponse = Snap::getSnapToken($payloadSnap);
            if (!$snapResponse || empty($snapResponse['token'])) {
                http_response_code(500);
                echo json_encode(['error' => 'Gagal membuat pembayaran']);
                return;
            }

            echo json_encode([
                'token' => $snapResponse['token'],
                'order_id' => $midtransOrderId,
                'fulfillment_order_id' => $fulfillmentOrderId,
            ]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Terjadi kesalahan saat membuat pembayaran']);
        }
    }

    public function syncStatus(): void
    {
        require_role(['pasien']);
        header('Content-Type: application/json');

        try {
            $payload = json_decode(file_get_contents('php://input'), true) ?? [];
            $fulfillmentId = (int) ($payload['fulfillment_order_id'] ?? 0);
            if (!$fulfillmentId) {
                http_response_code(400);
                echo json_encode(['error' => 'ID pemenuhan tidak valid']);
                return;
            }

            $order = $this->fulfillments->findWithRecommendation($fulfillmentId);
            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Pesanan tidak ditemukan']);
                return;
            }

            $user = user();
            $residentIds = $this->residentIdsForUser((int) $user['id']);
            if ($residentIds && !in_array((int) $order['resident_id'], $residentIds, true)) {
                http_response_code(403);
                echo json_encode(['error' => 'Anda tidak berhak mengakses pesanan ini']);
                return;
            }

            if (empty($order['midtrans_order_id'])) {
                echo json_encode([
                    'payment_status' => $order['payment_status'],
                    'recommendation_status' => $order['recommendation_status'],
                    'redirect_url' => url('?page=order-payment-detail&id=' . $fulfillmentId),
                ]);
                return;
            }

            $appConfig = require __DIR__ . '/../config.php';
            $serverKey = $appConfig['midtrans']['server_key'] ?? '';
            if (!$serverKey) {
                http_response_code(500);
                echo json_encode(['error' => 'Midtrans key belum dikonfigurasi']);
                return;
            }

            $isProduction = (bool) ($appConfig['midtrans']['is_production'] ?? false);
            $baseUrl = $isProduction ? 'https://api.midtrans.com/v2/' : 'https://api.sandbox.midtrans.com/v2/';
            $endpoint = $baseUrl . $order['midtrans_order_id'] . '/status';

            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $serverKey . ':');
            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($statusCode < 200 || $statusCode >= 300 || !$response) {
                http_response_code(500);
                echo json_encode(['error' => 'Gagal memeriksa status pembayaran']);
                return;
            }

            $data = json_decode($response, true);
            $midtransStatus = $data['transaction_status'] ?? 'unknown';
            $mappedStatus = $this->mapMidtransStatus($midtransStatus);

            $this->fulfillments->updateStatus($fulfillmentId, $mappedStatus);
            $recommendationStatus = $order['recommendation_status'];
            if ($mappedStatus === 'PAID') {
                $recommendationStatus = 'CONFIRMED';
                (new Recommendation())->updateStatus((int) $order['recommendation_id'], $recommendationStatus);
            }

            echo json_encode([
                'payment_status' => $mappedStatus,
                'recommendation_status' => $recommendationStatus,
                'midtrans_status' => $midtransStatus,
                'redirect_url' => url('?page=order-payment-detail&id=' . $fulfillmentId),
            ]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Terjadi kesalahan saat sinkronisasi status pembayaran']);
        }
    }

    public function orderPaymentDetail(): void
    {
        require_role(['pasien', 'super_admin', 'admin']);

        $id = (int) ($_GET['id'] ?? 0);
        $order = $this->fulfillments->findDetailed($id);
        if (!$order) {
            http_response_code(404);
            include __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $user = user();
        $isPatient = ($user['role'] ?? '') === 'pasien';
        if ($isPatient) {
            $residentIds = $this->residentIdsForUser((int) $user['id']);
            if ($residentIds && !in_array((int) $order['resident_id'], $residentIds, true)) {
                http_response_code(403);
                include __DIR__ . '/../Views/errors/403.php';
                return;
            }
        }

        $recommendation = (new Recommendation())->findWithItems((int) $order['recommendation_id']);
        if (!$recommendation) {
            http_response_code(404);
            include __DIR__ . '/../Views/errors/404.php';
            return;
        }

        include __DIR__ . '/../Views/orders/order_payment_detail.php';
    }

    private function buildSnapItems(array $recommendation): array
    {
        $items = [];
        $subtotal = 0;
        foreach ($recommendation['items'] as $item) {
            $price = (int) $item['price'];
            $qty = (int) $item['qty'];
            $items[] = [
                'id' => 'MED-' . $item['medicine_id'],
                'price' => $price,
                'quantity' => $qty,
                'name' => $item['medicine_name'],
            ];
            $subtotal += $price * $qty;
        }

        return [$items, $subtotal];
    }

    private function mapMidtransStatus(string $midtransStatus): string
    {
        if (in_array($midtransStatus, ['settlement', 'capture'], true)) {
            return 'PAID';
        }
        if ($midtransStatus === 'pending') {
            return 'PENDING';
        }
        if ($midtransStatus === 'expire') {
            return 'EXPIRED';
        }
        if ($midtransStatus === 'cancel') {
            return 'CANCELLED';
        }
        if ($midtransStatus === 'deny') {
            return 'FAILED';
        }
        return 'PENDING';
    }

    private function residentIdsForUser(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT resident_id FROM patient_children WHERE user_id = :user');
        $stmt->execute(['user' => $userId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    private function residentIdForUser(int $userId): ?int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT resident_id FROM patient_children WHERE user_id = :user LIMIT 1');
        $stmt->execute(['user' => $userId]);
        $row = $stmt->fetch();
        return $row ? (int) $row['resident_id'] : null;
    }
}
