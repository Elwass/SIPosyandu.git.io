<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Katalog Produk</span>
                <h2 class="section-title">Vitamin, PMT, dan Suplemen</h2>
                <p class="section-subtitle">Tambahkan ke keranjang lalu lanjutkan ke pembayaran via Midtrans.</p>
            </div>
            <a class="btn btn-outline-primary" href="<?= url('?page=cart') ?>">Lihat Keranjang</a>
        </div>
        <?php if (flash('success')): ?>
            <div class="alert alert-success"><?= flash('success') ?></div>
        <?php elseif (flash('error')): ?>
            <div class="alert alert-danger"><?= flash('error') ?></div>
        <?php endif; ?>
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="text-muted small mb-2">SKU: <?= htmlspecialchars($product['sku']) ?></p>
                            <p class="fw-semibold">Rp<?= number_format($product['price'], 0, ',', '.') ?></p>
                            <p class="text-muted small flex-grow-1"><?= nl2br(htmlspecialchars($product['description'] ?? '-')) ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge bg-soft-primary text-primary">Stok: <?= (int)$product['stock'] ?></span>
                                <form method="post" action="<?= url('?page=cart-add') ?>" class="d-flex align-items-center gap-2">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>" class="form-control form-control-sm" style="width:90px;">
                                    <button class="btn btn-primary btn-sm" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>Tambah</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (!$products): ?>
                <div class="col-12">
                    <div class="alert alert-info mb-0">Belum ada produk aktif.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
