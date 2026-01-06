<?php
class OrderController
{
    private Order $orders;
    private Product $products;
    private PaymentController $paymentController;

    public function __construct()
    {
        $this->orders = new Order();
        $this->products = new Product();
        $this->paymentController = new PaymentController();
    }

    public function checkoutForm(): void
    {
        require_role(['pasien']);
        $cart = $_SESSION['cart'] ?? [];
        $items = [];
        $total = 0;
        if ($cart) {
            $productIds = array_keys($cart);
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $stmt = Database::getInstance()->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
            $stmt->execute($productIds);
            $products = $stmt->fetchAll();
            foreach ($products as $product) {
                $qty = min($cart[$product['id']], $product['stock']);
                $subtotal = $product['price'] * $qty;
                $items[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }
        }
        include __DIR__ . '/../Views/patient/checkout.php';
    }

    public function processCheckout(): void
    {
        require_role(['pasien']);
        $user = user();
        $cart = $_SESSION['cart'] ?? [];
        if (!$cart) {
            flash('error', 'Keranjang masih kosong.');
            redirect('?page=cart');
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
                'product_id' => $product['id'],
                'quantity' => $qty,
                'price' => $product['price'],
                'subtotal' => $subtotal,
                'name' => $product['name'],
            ];
            $total += $subtotal;
        }

        if (!$items) {
            flash('error', 'Tidak ada produk valid untuk diproses.');
            redirect('?page=cart');
        }

        $pickupMethod = $_POST['pickup_method'] === 'DELIVERY' ? 'DELIVERY' : 'PICKUP';
        $address = $pickupMethod === 'DELIVERY' ? ($_POST['address'] ?? null) : null;

        $orderData = [
            'user_id' => $user['id'],
            'pickup_method' => $pickupMethod,
            'address' => $address,
            'total_amount' => $total,
            'payment_status' => 'UNPAID',
            'fulfillment_status' => 'DIPROSES',
            'snap_token' => null,
            'snap_redirect_url' => null,
        ];

        $orderId = $this->orders->create($orderData, array_map(function ($item) {
            return [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ];
        }, $items));

        $midtransOrderId = 'POSYANDU-' . $orderId;
        $tokenData = $this->paymentController->createSnapToken(
            $midtransOrderId,
            $total,
            $items,
            $user,
            ['order_id' => $orderId]
        );

        if (!$tokenData) {
            flash('error', 'Gagal memulai pembayaran, coba lagi.');
            redirect('?page=checkout');
        }

        $this->orders->updateSnapData($orderId, $tokenData['token'], $tokenData['redirect_url']);
        $_SESSION['cart'] = [];

        $order = $this->orders->find($orderId);
        $orderItems = $this->orders->items($orderId);
        $snapToken = $tokenData['token'];
        $clientKey = config('midtrans.client_key', '');
        include __DIR__ . '/../Views/patient/checkout_payment.php';
    }

    public function patientOrders(): void
    {
        require_role(['pasien']);
        $user = user();
        $orders = $this->orders->listByUser($user['id']);
        include __DIR__ . '/../Views/patient/orders.php';
    }

    public function orderDetail(): void
    {
        $user = user();
        if (!$user) {
            redirect('?page=login');
        }

        $id = (int)($_GET['id'] ?? 0);
        if ($user['role'] === 'super_admin') {
            $order = $this->orders->find($id);
        } else {
            $order = $this->orders->findByUser($id, $user['id']);
        }

        if (!$order) {
            http_response_code(404);
            include __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $items = $this->orders->items($order['id']);
        $view = $user['role'] === 'super_admin' ? __DIR__ . '/../Views/patient/order_detail.php' : __DIR__ . '/../Views/patient/order_detail.php';
        include $view;
    }

    public function adminIndex(): void
    {
        require_role(['super_admin']);
        $status = $_GET['status'] ?? null;
        $orders = $this->orders->listAll($status ?: null);
        include __DIR__ . '/../Views/orders/index.php';
    }

    public function updateFulfillment(): void
    {
        require_role(['super_admin']);
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['fulfillment_status'] ?? 'DIPROSES';
        $this->orders->updateFulfillment($id, $status);
        flash('success', 'Status pemenuhan diperbarui.');
        redirect('?page=admin-orders');
    }

    public function salesReport(): void
    {
        require_role(['super_admin']);
        $from = $_GET['from'] ?? null;
        $to = $_GET['to'] ?? null;
        $summary = $this->orders->reportTotals($from ?: null, $to ?: null);
        $topProducts = $this->orders->reportTopProducts($from ?: null, $to ?: null);
        include __DIR__ . '/../Views/orders/report.php';
    }
}
