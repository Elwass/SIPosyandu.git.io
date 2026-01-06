<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Keranjang</span>
                <h2 class="section-title">Ringkasan Belanja</h2>
            </div>
            <a class="btn btn-outline-primary" href="<?= url('?page=products') ?>">Kembali ke Produk</a>
        </div>
        <?php if (flash('success')): ?>
            <div class="alert alert-success"><?= flash('success') ?></div>
        <?php elseif (flash('error')): ?>
            <div class="alert alert-danger"><?= flash('error') ?></div>
        <?php endif; ?>
        <?php if ($items): ?>
            <form method="post" action="<?= url('?page=cart-update') ?>">
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($item['product']['image_url'])): ?>
                                                <img src="<?= htmlspecialchars($item['product']['image_url']) ?>" alt="<?= htmlspecialchars($item['product']['name']) ?>" width="60">
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-semibold"><?= htmlspecialchars($item['product']['name']) ?></div>
                                                <div class="text-muted small">SKU: <?= htmlspecialchars($item['product']['sku']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Rp<?= number_format($item['product']['price'], 0, ',', '.') ?></td>
                                    <td style="width: 120px;">
                                        <input type="number" name="quantities[<?= $item['product']['id'] ?>]" min="1" max="<?= (int)$item['product']['stock'] ?>" value="<?= $item['quantity'] ?>" class="form-control">
                                    </td>
                                    <td>Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                    <td>
                                        <form method="post" action="<?= url('?page=cart-remove') ?>" onsubmit="return confirm('Hapus produk ini?')">
                                            <input type="hidden" name="product_id" value="<?= $item['product']['id'] ?>">
                                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-3">
                    <div class="fw-semibold">Total: Rp<?= number_format($total, 0, ',', '.') ?></div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" type="submit">Perbarui Keranjang</button>
                        <a class="btn btn-primary" href="<?= url('?page=checkout') ?>">Checkout</a>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-info">Keranjang masih kosong.</div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
