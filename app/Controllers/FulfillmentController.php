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
            'patient_id' => $recommendation['patient_id'],
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
                'first_name' => $recommendation['patient_name'] ?? 'Pasien',
                'email' => '',
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
}
