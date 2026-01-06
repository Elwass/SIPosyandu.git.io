<?php
class ProductController
{
    private Product $products;

    public function __construct()
    {
        $this->products = new Product();
    }

    public function catalog(): void
    {
        require_role(['pasien']);
        $products = $this->products->active();
        include __DIR__ . '/../Views/patient/products.php';
    }

    public function adminIndex(): void
    {
        require_role(['super_admin']);
        $products = $this->products->all();
        include __DIR__ . '/../Views/products/index.php';
    }

    public function create(): void
    {
        require_role(['super_admin']);
        include __DIR__ . '/../Views/products/create.php';
    }

    public function store(): void
    {
        require_role(['super_admin']);
        $data = [
            'name' => $_POST['name'],
            'sku' => $_POST['sku'],
            'price' => (float)$_POST['price'],
            'stock' => (int)$_POST['stock'],
            'description' => $_POST['description'] ?? null,
            'image_url' => $_POST['image_url'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        $this->products->create($data);
        flash('success', 'Produk berhasil ditambahkan.');
        redirect('?page=admin-products');
    }

    public function edit(): void
    {
        require_role(['super_admin']);
        $id = (int)($_GET['id'] ?? 0);
        $product = $this->products->find($id);
        if (!$product) {
            http_response_code(404);
            include __DIR__ . '/../Views/errors/404.php';
            return;
        }
        include __DIR__ . '/../Views/products/edit.php';
    }

    public function update(): void
    {
        require_role(['super_admin']);
        $id = (int)($_POST['id'] ?? 0);
        $product = $this->products->find($id);
        if (!$product) {
            http_response_code(404);
            include __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $data = [
            'name' => $_POST['name'],
            'sku' => $_POST['sku'],
            'price' => (float)$_POST['price'],
            'stock' => (int)$_POST['stock'],
            'description' => $_POST['description'] ?? null,
            'image_url' => $_POST['image_url'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        $this->products->update($id, $data);
        flash('success', 'Produk berhasil diperbarui.');
        redirect('?page=admin-products');
    }

    public function destroy(): void
    {
        require_role(['super_admin']);
        $id = (int)($_POST['id'] ?? 0);
        $this->products->delete($id);
        flash('success', 'Produk berhasil dihapus.');
        redirect('?page=admin-products');
    }
}
