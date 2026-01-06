<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Detail Pesanan</span>
                <h2 class="section-title">Order #<?= $order['id'] ?></h2>
                <p class="section-subtitle">Metode: <?= htmlspecialchars($order['pickup_method']) ?> | Pembayaran: <?= htmlspecialchars($order['payment_status']) ?> | Pemenuhan: <?= htmlspecialchars($order['fulfillment_status']) ?></p>
            </div>
            <a class="btn btn-outline-primary" href="<?= url('?page=orders') ?>">Kembali</a>
        </div>
        <div class="surface-card mb-4">
            <div class="surface-header"><h5>Alamat / Catatan</h5></div>
            <div class="surface-body">
                <?php if ($order['pickup_method'] === 'DELIVERY'): ?>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($order['address'] ?? '-')) ?></p>
                <?php else: ?>
                    <p class="mb-0 text-muted">Ambil di lokasi Posyandu.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="surface-card">
            <div class="surface-header"><h5>Item</h5></div>
            <div class="surface-body">
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td>Rp<?= number_format($item['price'], 0, ',', '.') ?></td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td>Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between fw-semibold mt-3">
                    <span>Total</span>
                    <span>Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
