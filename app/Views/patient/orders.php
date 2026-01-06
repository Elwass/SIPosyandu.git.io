<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Pesanan Saya</span>
                <h2 class="section-title">Riwayat Pembelian</h2>
            </div>
        </div>
        <div class="surface-card">
            <div class="surface-body">
                <?php if ($orders): ?>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Total</th>
                                    <th>Pembayaran</th>
                                    <th>Pemenuhan</th>
                                    <th>Tanggal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?= $order['id'] ?></td>
                                        <td>Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                        <td><span class="badge bg-soft-primary text-primary"><?= htmlspecialchars($order['payment_status']) ?></span></td>
                                        <td><span class="badge bg-soft-secondary text-secondary"><?= htmlspecialchars($order['fulfillment_status']) ?></span></td>
                                        <td><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td>
                                        <td><a class="btn btn-sm btn-outline-primary" href="<?= url('?page=order-detail&id=' . $order['id']) ?>">Detail</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="mb-0 text-muted">Belum ada pesanan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
