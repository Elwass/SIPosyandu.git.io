<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Manajemen Produk</span>
                <h2 class="section-title">Daftar Produk</h2>
            </div>
            <a class="btn btn-primary" href="<?= url('?page=admin-products-create') ?>">Tambah Produk</a>
        </div>
        <?php if (flash('success')): ?>
            <div class="alert alert-success"><?= flash('success') ?></div>
        <?php elseif (flash('error')): ?>
            <div class="alert alert-danger"><?= flash('error') ?></div>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>SKU</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['sku']) ?></td>
                            <td>Rp<?= number_format($product['price'], 0, ',', '.') ?></td>
                            <td><?= (int)$product['stock'] ?></td>
                            <td><span class="badge bg-soft-primary text-primary"><?= $product['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="<?= url('?page=admin-products-edit&id=' . $product['id']) ?>">Edit</a>
                                <form class="d-inline" method="post" action="<?= url('?page=admin-products-delete') ?>" onsubmit="return confirm('Hapus produk ini?')">
                                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
