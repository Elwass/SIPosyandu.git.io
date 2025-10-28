<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Data Penimbangan</h2>
    </div>
    <?php if ($message = flash('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">Input Penimbangan</h5>
            <form method="post" action="?page=measurements-store">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Warga</label>
                        <select name="resident_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach ($residents as $resident): ?>
                                <option value="<?= $resident['id'] ?>"><?= htmlspecialchars($resident['name']) ?> (<?= htmlspecialchars($resident['category']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Berat (kg)</label>
                        <input type="number" step="0.1" name="weight" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tinggi (cm)</label>
                        <input type="number" step="0.1" name="height" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">LILA (cm)</label>
                        <input type="number" step="0.1" name="muac" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="measured_at" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button class="btn btn-success" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
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
                                <td><span class="badge bg-success badge-status"><?= htmlspecialchars(str_replace('_', ' ', $measurement['nutritional_status'])) ?></span></td>
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
<?php include __DIR__ . '/../layouts/footer.php'; ?>
