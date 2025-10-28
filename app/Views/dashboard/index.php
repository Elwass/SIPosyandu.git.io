<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container">
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Ibu Hamil</h6>
                    <h3><?= $residentCounts['pregnant'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Balita</h6>
                    <h3><?= $residentCounts['toddler'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Lansia</h6>
                    <h3><?= $residentCounts['elderly'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted">Imunisasi Terjadwal</h6>
                    <h3><?= count($upcomingImmunizations) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">Statistik Status Gizi</div>
                <div class="card-body">
                    <?php if ($measurementSummary): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($measurementSummary as $status => $total): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars(str_replace('_', ' ', $status)) ?>
                                    <span class="badge bg-success rounded-pill"><?= $total ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Belum ada data penimbangan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">Imunisasi Mendatang</div>
                <div class="card-body">
                    <?php if ($upcomingImmunizations): ?>
                        <div class="timeline">
                            <?php foreach ($upcomingImmunizations as $item): ?>
                                <div class="mb-3">
                                    <h6 class="mb-1"><?= htmlspecialchars($item['resident_name']) ?> - <?= htmlspecialchars($item['vaccine_name']) ?></h6>
                                    <p class="mb-1 small text-muted"><?= date('d M Y', strtotime($item['schedule_date'])) ?> &middot; Kontak: <?= htmlspecialchars($item['phone']) ?></p>
                                    <span class="badge bg-warning text-dark"><?= strtoupper($item['status']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Belum ada jadwal imunisasi yang akan datang.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-success text-white">Penimbangan Terbaru</div>
        <div class="card-body">
            <?php if ($recentMeasurements): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Berat (kg)</th>
                                <th>Tinggi (cm)</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentMeasurements as $measurement): ?>
                                <tr>
                                    <td><?= htmlspecialchars($measurement['resident_name']) ?></td>
                                    <td><?= $measurement['weight'] ?></td>
                                    <td><?= $measurement['height'] ?></td>
                                    <td><span class="badge bg-success badge-status"><?= htmlspecialchars(str_replace('_', ' ', $measurement['nutritional_status'])) ?></span></td>
                                    <td><?= date('d M Y', strtotime($measurement['measured_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">Belum ada data penimbangan.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
