<?php
class CartController
{
    private Product $products;

    public function __construct()
    {
        $this->products = new Product();
    }

    private function &cart(): array
    {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        return $_SESSION['cart'];
    }

    public function index(): void
    {
        require_role(['pasien']);
        $cart = $this->cart();
        $items = [];
        $total = 0;
        if ($cart) {
            $productIds = array_keys($cart);
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $stmt = Database::getInstance()->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
            $stmt->execute($productIds);
            $products = $stmt->fetchAll();
            foreach ($products as $product) {
                $qty = $cart[$product['id']];
                $subtotal = $product['price'] * $qty;
                $items[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ];
                $total += $subtotal;
            }
        }
        include __DIR__ . '/../Views/patient/cart.php';
    }

    public function add(): void
    {
        require_role(['pasien']);
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $product = $this->products->find($productId);
        if (!$product || !$product['is_active']) {
            flash('error', 'Produk tidak ditemukan atau tidak aktif.');
            redirect('?page=products');
        }

        $cart = &$this->cart();
        $current = $cart[$productId] ?? 0;
        $cart[$productId] = min($product['stock'], $current + $quantity);

        flash('success', 'Produk ditambahkan ke keranjang.');
        redirect('?page=products');
    }

    public function update(): void
    {
        require_role(['pasien']);
        $cart = &$this->cart();
        $quantities = $_POST['quantities'] ?? [];
        foreach ($quantities as $productId => $qty) {
            $productId = (int)$productId;
            $qty = max(0, (int)$qty);
            if ($qty === 0) {
                unset($cart[$productId]);
                continue;
            }
            $product = $this->products->find($productId);
            if ($product) {
                $cart[$productId] = min($qty, $product['stock']);
            }
        }
        flash('success', 'Keranjang diperbarui.');
        redirect('?page=cart');
    }

    public function remove(): void
    {
        require_role(['pasien']);
        $productId = (int)($_POST['product_id'] ?? 0);
        $cart = &$this->cart();
        unset($cart[$productId]);
        flash('success', 'Produk dihapus dari keranjang.');
        redirect('?page=cart');
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }
}
