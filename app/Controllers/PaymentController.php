<?php
class PaymentController
{
    public function createSnapToken(string $orderId, float $amount, array $items, array $customer, array $metadata = []): ?array
    {
        $appConfig = require __DIR__ . '/../config.php';
        $serverKey = $appConfig['midtrans']['server_key'] ?? '';
        $clientKey = $appConfig['midtrans']['client_key'] ?? '';
        if ($serverKey === '' || $clientKey === '') {
            return null;
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'item_details' => array_map(function ($item) {
                return [
                    'id' => $item['product_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'name' => substr($item['name'], 0, 50),
                ];
            }, $items),
            'customer_details' => [
                'first_name' => $customer['name'] ?? 'Pasien',
                'email' => $customer['email'] ?? 'user@example.com',
            ],
            'callbacks' => [
                'finish' => url('?page=orders'),
                'error' => url('?page=orders'),
            ],
            'metadata' => $metadata,
        ];

        $isProduction = $appConfig['midtrans']['is_production'] ?? false;
        $endpoint = $isProduction ? 'https://app.midtrans.com/snap/v1/transactions' : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
        curl_setopt($ch, CURLOPT_USERPWD, $serverKey . ':');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status >= 200 && $status < 300 && $response) {
            $data = json_decode($response, true);
            if (isset($data['token'])) {
                return [
                    'token' => $data['token'],
                    'redirect_url' => $data['redirect_url'] ?? null,
                ];
            }
        }

        return null;
    }
}
