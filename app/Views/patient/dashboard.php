<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--dashboard">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Halo, <?= htmlspecialchars($patient['name']) ?></span>
            <h2 class="section-title">Ringkasan Keluarga</h2>
            <p class="section-subtitle">Pantau status BPJS Anda dan perkembangan balita yang terhubung.</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <span class="stat-label">Jumlah Balita</span>
                    <span class="stat-value"><?= count($children) ?></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <span class="stat-label">Status BPJS</span>
                    <span class="stat-value text-uppercase">
                        <?= $bpjs ? htmlspecialchars($bpjs['status_bpjs']) : 'belum diisi' ?>
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <span class="stat-label">Jadwal Imunisasi Akan Datang</span>
                    <span class="stat-value"><?= count($upcomingImmunizations) ?></span>
                </div>
            </div>
        </div>

        <div class="surface-card mb-4">
            <div class="surface-header">
                <h3>Daftar Balita</h3>
            </div>
            <div class="surface-body">
                <?php if ($children): ?>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Terakhir Ditimbang</th>
                                    <th>BB/TB Terakhir</th>
                                    <th>Status Gizi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($children as $child): ?>
                                    <?php $latest = $latestMeasurements[$child['id']] ?? null; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($child['name']) ?></td>
                                        <td><?= date('d M Y', strtotime($child['birth_date'])) ?></td>
                                        <td><?= $latest ? date('d M Y', strtotime($latest['measured_at'])) : '-' ?></td>
                                        <td><?= $latest ? $latest['weight'] . ' kg / ' . $latest['height'] . ' cm' : '-' ?></td>
                                        <td>
                                            <?php if ($latest): ?>
                                                <span class="badge bg-soft-primary text-primary"><?= htmlspecialchars(str_replace('_', ' ', $latest['nutritional_status'])) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Belum ada data</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Belum ada balita yang ditambahkan. Silakan tambahkan melalui halaman Profil.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="surface-card">
            <div class="surface-header">
                <h3>Imunisasi Terjadwal</h3>
            </div>
            <div class="surface-body">
                <?php if ($upcomingImmunizations): ?>
                    <div class="timeline">
                        <?php foreach ($upcomingImmunizations as $item): ?>
                            <div class="timeline-item">
                                <h6 class="mb-1"><?= htmlspecialchars($item['resident_name']) ?> - <?= htmlspecialchars($item['vaccine_name']) ?></h6>
                                <p class="mb-1 small text-muted">Jadwal: <?= date('d M Y', strtotime($item['schedule_date'])) ?></p>
                                <span class="badge bg-soft-warning text-warning">Terjadwal</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Belum ada jadwal imunisasi terdekat.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
