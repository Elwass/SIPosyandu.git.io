<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--page">
    <div class="container">
        <div class="section-heading">
            <span class="section-eyebrow">Pemantauan Gizi</span>
            <h2 class="section-title">Data Penimbangan</h2>
            <p class="section-subtitle">Catat hasil timbang balita secara berkala untuk mengukur status gizi dan tindak lanjut.</p>
        </div>
        <?php if ($message = flash('success')): ?>
            <div class="alert alert-success alert-modern"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($message = flash('error')): ?>
            <div class="alert alert-danger alert-modern"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <div class="surface-card mb-5">
            <div class="surface-header">
                <h3>Input Penimbangan</h3>
            </div>
            <div class="surface-body">
                <form method="post" action="<?= url('?page=measurements-store') ?>" class="form-grid">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label">Warga</label>
                            <select name="resident_id" class="form-select form-select-modern" required>
                                <option value="">-- Pilih --</option>
                                <?php foreach ($residents as $resident): ?>
                                    <option value="<?= $resident['id'] ?>"><?= htmlspecialchars($resident['name']) ?> (<?= htmlspecialchars($resident['category']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Berat (kg)</label>
                            <input type="number" step="0.1" name="weight" class="form-control form-control-modern" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tinggi (cm)</label>
                            <input type="number" step="0.1" name="height" class="form-control form-control-modern" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">LILA (cm)</label>
                            <input type="number" step="0.1" name="muac" class="form-control form-control-modern">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="measured_at" class="form-control form-control-modern" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control form-control-modern" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="form-actions text-end mt-4">
                        <button class="btn btn-primary" type="submit">Simpan Penimbangan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="surface-card">
            <div class="surface-header">
                <h3>Riwayat Penimbangan</h3>
            </div>
            <div class="surface-body">
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Berat</th>
                                <th>Tinggi</th>
                                <th>Status Gizi</th>
                                <th>Tanggal</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($measurements as $measurement): ?>
                                <tr>
                                    <td><?= htmlspecialchars($measurement['resident_name']) ?></td>
                                    <td><?= htmlspecialchars($measurement['category']) ?></td>
                                    <td><?= htmlspecialchars($measurement['weight']) ?> kg</td>
                                    <td><?= htmlspecialchars($measurement['height']) ?> cm</td>
                                    <td><span class="badge bg-soft-success text-success"><?= htmlspecialchars(str_replace('_', ' ', $measurement['nutritional_status'])) ?></span></td>
                                    <td><?= date('d M Y', strtotime($measurement['measured_at'])) ?></td>
                                    <td><?= htmlspecialchars($measurement['notes']) ?></td>
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
