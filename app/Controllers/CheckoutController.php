<?php

require_once __DIR__ . '/../Libraries/Midtrans.php';

use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController
{
    private Product $products;

    public function __construct()
    {
        $this->products = new Product();
    }

    public function createPayment(): void
    {
        require_role(['pasien']);
        header('Content-Type: application/json');

        $cart = $_SESSION['cart'] ?? [];
        if (!$cart) {
            http_response_code(400);
            echo json_encode(['error' => 'Keranjang kosong']);
            return;
        }

        $productIds = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stmt = Database::getInstance()->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($productIds);
        $products = $stmt->fetchAll();

        $items = [];
        $total = 0;
        foreach ($products as $product) {
            $qty = min($cart[$product['id']], $product['stock']);
            if ($qty <= 0) {
                continue;
            }
            $subtotal = $product['price'] * $qty;
            $items[] = [
                'id' => $product['id'],
                'price' => $product['price'],
                'quantity' => $qty,
                'name' => substr($product['name'], 0, 50),
            ];
            $total += $subtotal;
        }

        if ($total <= 0 || !$items) {
            http_response_code(400);
            echo json_encode(['error' => 'Tidak ada item valid untuk dibayar']);
            return;
        }

        Config::$serverKey = config('midtrans.server_key', Config::$serverKey);
        Config::$clientKey = config('midtrans.client_key', Config::$clientKey);
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $user = user();
        $payload = [
            'transaction_details' => [
                'order_id' => 'POSYANDU-' . time(),
                'gross_amount' => $total,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $user['name'] ?? 'Pasien',
                'email' => $user['email'] ?? 'user@example.com',
            ],
            'callbacks' => [
                'finish' => url('?page=orders'),
                'error' => url('?page=orders'),
            ],
        ];

        $result = Snap::getSnapToken($payload);
        if (!$result || !isset($result['token'])) {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal membuat transaksi']);
            return;
        }

        echo json_encode([
            'token' => $result['token'],
            'redirect_url' => $result['redirect_url'] ?? null,
        ]);
    }
}
