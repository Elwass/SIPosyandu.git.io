<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--page">
    <div class="container">
        <div class="section-heading d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
            <div>
                <span class="section-eyebrow">Laporan Otomatis</span>
                <h2 class="section-title mb-0">Rekap Penimbangan &amp; Gizi</h2>
                <p class="section-subtitle">Unduh laporan PDF untuk kebutuhan pelaporan Puskesmas dan monitoring kesehatan anak.</p>
            </div>
            <a href="<?= url('?page=reports-download') ?>" class="btn btn-primary mt-3 mt-lg-0">Cetak PDF</a>
        </div>
        <div class="surface-card">
            <div class="surface-header">
                <h3>Data Penimbangan</h3>
            </div>
            <div class="surface-body">
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Berat</th>
                                <th>Tinggi</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($measurements as $measurement): ?>
                                <tr>
                                    <td><?= htmlspecialchars($measurement['resident_name']) ?></td>
                                    <td><?= htmlspecialchars($measurement['weight']) ?> kg</td>
                                    <td><?= htmlspecialchars($measurement['height']) ?> cm</td>
                                    <td><span class="badge bg-soft-success text-success"><?= htmlspecialchars($measurement['nutritional_status']) ?></span></td>
                                    <td><?= date('d M Y', strtotime($measurement['measured_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
