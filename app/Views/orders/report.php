<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="section-eyebrow">Laporan Penjualan</span>
                <h2 class="section-title">Ringkasan</h2>
            </div>
            <form class="d-flex gap-2" method="get" action="">
                <input type="hidden" name="page" value="admin-sales-report">
                <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($_GET['from'] ?? '') ?>">
                <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($_GET['to'] ?? '') ?>">
                <button class="btn btn-outline-primary" type="submit">Filter</button>
            </form>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <span class="stat-label">Total Transaksi</span>
                    <span class="stat-value"><?= (int)($summary['total_orders'] ?? 0) ?></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <span class="stat-label">Total Pendapatan</span>
                    <span class="stat-value">Rp<?= number_format($summary['total_amount'] ?? 0, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
        <div class="surface-card mt-4">
            <div class="surface-header"><h5>Produk Terlaris</h5></div>
            <div class="surface-body">
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Penjualan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topProducts as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= (int)$product['total_qty'] ?></td>
                                    <td>Rp<?= number_format($product['total_sales'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$topProducts): ?>
                                <tr><td colspan="3" class="text-center text-muted">Belum ada data.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
