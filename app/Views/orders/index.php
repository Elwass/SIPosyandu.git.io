<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Pesanan</span>
                <h2 class="section-title">Semua Pesanan</h2>
            </div>
            <form class="d-flex gap-2" method="get" action="">
                <input type="hidden" name="page" value="admin-orders">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="UNPAID" <?= ($_GET['status'] ?? '') === 'UNPAID' ? 'selected' : '' ?>>UNPAID</option>
                    <option value="PAID" <?= ($_GET['status'] ?? '') === 'PAID' ? 'selected' : '' ?>>PAID</option>
                    <option value="EXPIRED" <?= ($_GET['status'] ?? '') === 'EXPIRED' ? 'selected' : '' ?>>EXPIRED</option>
                    <option value="CANCELLED" <?= ($_GET['status'] ?? '') === 'CANCELLED' ? 'selected' : '' ?>>CANCELLED</option>
                    <option value="REFUNDED" <?= ($_GET['status'] ?? '') === 'REFUNDED' ? 'selected' : '' ?>>REFUNDED</option>
                </select>
                <button class="btn btn-outline-primary" type="submit">Filter</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pasien</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Pemenuhan</th>
                        <th>Dibuat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['user_name']) ?></td>
                            <td>Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                            <td><span class="badge bg-soft-primary text-primary"><?= htmlspecialchars($order['payment_status']) ?></span></td>
                            <td>
                                <form method="post" action="<?= url('?page=admin-order-fulfillment') ?>" class="d-flex align-items-center gap-2">
                                    <input type="hidden" name="id" value="<?= $order['id'] ?>">
                                    <select name="fulfillment_status" class="form-select form-select-sm">
                                        <option value="DIPROSES" <?= $order['fulfillment_status'] === 'DIPROSES' ? 'selected' : '' ?>>DIPROSES</option>
                                        <option value="SIAP_DIAMBIL" <?= $order['fulfillment_status'] === 'SIAP_DIAMBIL' ? 'selected' : '' ?>>SIAP_DIAMBIL</option>
                                        <option value="DIKIRIM" <?= $order['fulfillment_status'] === 'DIKIRIM' ? 'selected' : '' ?>>DIKIRIM</option>
                                        <option value="SELESAI" <?= $order['fulfillment_status'] === 'SELESAI' ? 'selected' : '' ?>>SELESAI</option>
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary">Update</button>
                                </form>
                            </td>
                            <td><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td>
                            <td><a class="btn btn-sm btn-outline-secondary" href="<?= url('?page=order-detail&id=' . $order['id']) ?>">Detail</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
