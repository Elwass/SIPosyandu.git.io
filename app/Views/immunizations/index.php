<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Imunisasi</h2>
    </div>
    <?php if ($message = flash('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">Tambah Jadwal</h5>
            <form method="post" action="?page=immunizations-store">
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
                    <div class="col-md-3">
                        <label class="form-label">Vaksin</label>
                        <input type="text" name="vaccine_name" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jadwal</label>
                        <input type="date" name="schedule_date" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="scheduled">Terjadwal</option>
                            <option value="completed">Selesai</option>
                            <option value="pending">Tertunda</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Realisasi</label>
                        <input type="date" name="administered_date" class="form-control">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="notes" class="form-control">
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
                            <th>Vaksin</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th>Realisasi</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($immunizations as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['resident_name']) ?></td>
                                <td><?= htmlspecialchars($item['vaccine_name']) ?></td>
                                <td><?= date('d M Y', strtotime($item['schedule_date'])) ?></td>
                                <td><span class="badge bg-info text-dark"><?= strtoupper($item['status']) ?></span></td>
                                <td><?= $item['administered_date'] ? date('d M Y', strtotime($item['administered_date'])) : '-' ?></td>
                                <td><?= htmlspecialchars($item['notes']) ?></td>
                                <td>
                                    <?php if (in_array(user()['role'], ['super_admin', 'admin', 'midwife'], true) && $item['status'] !== 'completed'): ?>
                                        <a href="?page=immunizations-complete&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-success">Tandai Selesai</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
