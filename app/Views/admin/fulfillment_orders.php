<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Pemenuhan</span>
                <h2 class="section-title">Daftar Pemenuhan</h2>
            </div>
        </div>
        <div class="surface-card">
            <div class="surface-body">
                <?php if (!empty($orders)): ?>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Warga</th>
                                    <th>Metode</th>
                                    <th>Status Pembayaran</th>
                                    <th>Total</th>
                                    <th>Dibuat</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?= htmlspecialchars($order['id']) ?></td>
                                        <td><?= htmlspecialchars($order['resident_name']) ?></td>
                                        <td><?= htmlspecialchars($order['fulfillment_method']) ?></td>
                                        <td><span class="badge bg-soft-primary text-primary"><?= htmlspecialchars($order['payment_status']) ?></span></td>
                                        <td>Rp <?= number_format((int) $order['total_amount'], 0, ',', '.') ?></td>
                                        <td><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td>
                                        <td class="text-end">
                                            <a class="btn btn-sm btn-outline-primary" href="<?= url('?page=recommendation-detail&id=' . $order['recommendation_id']) ?>">Rekomendasi</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Belum ada data pemenuhan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
