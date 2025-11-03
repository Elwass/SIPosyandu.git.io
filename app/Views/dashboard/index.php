<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--dashboard">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Ringkasan Posyandu</span>
            <h2 class="section-title">Dashboard Monitoring Kesehatan</h2>
            <p class="section-subtitle">Pantau perkembangan gizi balita, jadwal imunisasi, dan aktivitas pelayanan secara real-time.</p>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <span class="stat-label">Ibu Hamil</span>
                    <span class="stat-value"><?= $residentCounts['pregnant'] ?? 0 ?></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <span class="stat-label">Balita</span>
                    <span class="stat-value"><?= $residentCounts['toddler'] ?? 0 ?></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <span class="stat-label">Lansia</span>
                    <span class="stat-value"><?= $residentCounts['elderly'] ?? 0 ?></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <span class="stat-label">Imunisasi Terjadwal</span>
                    <span class="stat-value"><?= count($upcomingImmunizations) ?></span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="surface-card h-100">
                    <div class="surface-header">
                        <h3>Statistik Status Gizi</h3>
                    </div>
                    <div class="surface-body">
                        <?php if ($measurementSummary): ?>
                            <ul class="list-group list-group-flush list-group-modern">
                                <?php foreach ($measurementSummary as $status => $total): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><?= htmlspecialchars(str_replace('_', ' ', $status)) ?></span>
                                        <span class="badge rounded-pill bg-soft-primary text-primary"><?= $total ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted mb-0">Belum ada data penimbangan.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="surface-card h-100">
                    <div class="surface-header">
                        <h3>Imunisasi Mendatang</h3>
                    </div>
                    <div class="surface-body">
                        <?php if ($upcomingImmunizations): ?>
                            <div class="timeline">
                                <?php foreach ($upcomingImmunizations as $item): ?>
                                    <div class="timeline-item">
                                        <h6 class="mb-1"><?= htmlspecialchars($item['resident_name']) ?> - <?= htmlspecialchars($item['vaccine_name']) ?></h6>
                                        <p class="mb-1 small text-muted"><?= date('d M Y', strtotime($item['schedule_date'])) ?> · Kontak: <?= htmlspecialchars($item['phone']) ?></p>
                                        <span class="badge bg-soft-warning text-warning"><?= strtoupper($item['status']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Belum ada jadwal imunisasi yang akan datang.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="surface-card mt-4">
            <div class="surface-header">
                <h3>Penimbangan Terbaru</h3>
            </div>
            <div class="surface-body">
                <?php if ($recentMeasurements): ?>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle">
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
                                        <td><span class="badge bg-soft-success text-success"><?= htmlspecialchars(str_replace('_', ' ', $measurement['nutritional_status'])) ?></span></td>
                                        <td><?= date('d M Y', strtotime($measurement['measured_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Belum ada data penimbangan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
