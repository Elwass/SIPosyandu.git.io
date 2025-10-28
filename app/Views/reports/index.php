<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Laporan</h2>
        <a href="?page=reports-download" class="btn btn-success">Cetak PDF</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
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
                                <td><?= htmlspecialchars($measurement['nutritional_status']) ?></td>
                                <td><?= date('d M Y', strtotime($measurement['measured_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
