<?php
use Midtrans\Config;
use Midtrans\Snap;

class BookingController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    private function findBooking(string $bookingCode): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM bookings WHERE booking_code = ? LIMIT 1');
        $stmt->execute([$bookingCode]);
        $booking = $stmt->fetch();

        return $booking ?: null;
    }

    private function initializeMidtrans(): array
    {
        $appConfig = require __DIR__ . '/../config.php';
        Config::$serverKey = $appConfig['midtrans']['server_key'] ?? '';
        Config::$clientKey = $appConfig['midtrans']['client_key'] ?? '';
        Config::$isProduction = $appConfig['midtrans']['is_production'] ?? false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        return $appConfig;
    }

    public function show(string $bookingCode): void
    {
        $booking = $this->findBooking($bookingCode);
        if (!$booking) {
            http_response_code(404);
            include __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $appConfig = $this->initializeMidtrans();
        include __DIR__ . '/../Views/booking/detail.php';
    }

    public function pay(string $bookingCode): void
    {
        header('Content-Type: application/json');
        $booking = $this->findBooking($bookingCode);
        if (!$booking) {
            http_response_code(404);
            echo json_encode(['error' => 'Booking tidak ditemukan']);
            return;
        }

        $this->initializeMidtrans();
        $payload = [
            'transaction_details' => [
                'order_id' => $booking['order_id'],
                'gross_amount' => (int) $booking['total_price'],
            ],
            'customer_details' => [
                'first_name' => $booking['user_name'],
                'email' => $booking['email'],
                'phone' => $booking['phone'],
            ],
            'item_details' => [
                [
                    'id' => $booking['package_name'] ?: $booking['service_name'],
                    'price' => (int) $booking['total_price'],
                    'quantity' => 1,
                    'name' => $booking['service_name'] ?: 'Layanan',
                ],
            ],
        ];

        $tokenData = Snap::getSnapToken($payload);

        if (!$tokenData || empty($tokenData['token'])) {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal membuat token pembayaran']);
            return;
        }

        $stmt = $this->db->prepare('UPDATE bookings SET snap_token = ? WHERE id = ?');
        $stmt->execute([$tokenData['token'], $booking['id']]);

        echo json_encode(['token' => $tokenData['token']]);
    }

    public function checkStatus(string $bookingCode): void
    {
        header('Content-Type: application/json');
        $booking = $this->findBooking($bookingCode);
        if (!$booking) {
            http_response_code(404);
            echo json_encode(['error' => 'Booking tidak ditemukan']);
            return;
        }

        $appConfig = $this->initializeMidtrans();
        $baseUrl = !empty($appConfig['midtrans']['is_production'])
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
        $endpoint = $baseUrl . '/v2/' . rawurlencode($booking['order_id']) . '/status';

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, Config::$serverKey . ':');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode < 200 || $statusCode >= 300 || !$response) {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal mengambil status pembayaran']);
            return;
        }

        $data = json_decode($response, true);
        $transactionStatus = $data['transaction_status'] ?? 'pending';
        $mappedStatus = 'pending';
        if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
            $mappedStatus = 'paid';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'], true)) {
            $mappedStatus = 'failed';
        }

        if ($mappedStatus !== $booking['payment_status']) {
            $stmt = $this->db->prepare('UPDATE bookings SET payment_status = ? WHERE id = ?');
            $stmt->execute([$mappedStatus, $booking['id']]);
        }

        echo json_encode([
            'status' => $mappedStatus,
            'midtrans' => $data,
        ]);
    }
}
