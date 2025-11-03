<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--page">
    <div class="container">
        <div class="section-heading">
            <span class="section-eyebrow">Jadwal Imunisasi</span>
            <h2 class="section-title">Manajemen Imunisasi Anak</h2>
            <p class="section-subtitle">Atur jadwal vaksin, kirim reminder, dan tandai penyelesaian imunisasi secara tepat waktu.</p>
        </div>
        <?php if ($message = flash('success')): ?>
            <div class="alert alert-success alert-modern"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <div class="surface-card mb-5">
            <div class="surface-header">
                <h3>Tambah Jadwal Imunisasi</h3>
            </div>
            <div class="surface-body">
                <form method="post" action="<?= url('?page=immunizations-store') ?>" class="form-grid">
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
                        <div class="col-md-3">
                            <label class="form-label">Nama Vaksin</label>
                            <input type="text" name="vaccine_name" class="form-control form-control-modern" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jadwal</label>
                            <input type="date" name="schedule_date" class="form-control form-control-modern" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select form-select-modern" required>
                                <option value="scheduled">Terjadwal</option>
                                <option value="completed">Selesai</option>
                                <option value="pending">Tertunda</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Realisasi</label>
                            <input type="date" name="administered_date" class="form-control form-control-modern">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="notes" class="form-control form-control-modern">
                        </div>
                    </div>
                    <div class="form-actions text-end mt-4">
                        <button class="btn btn-primary" type="submit">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="surface-card">
            <div class="surface-header">
                <h3>Daftar Jadwal Imunisasi</h3>
            </div>
            <div class="surface-body">
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Vaksin</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                                <th>Realisasi</th>
                                <th>Catatan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($immunizations as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['resident_name']) ?></td>
                                    <td><?= htmlspecialchars($item['vaccine_name']) ?></td>
                                    <td><?= date('d M Y', strtotime($item['schedule_date'])) ?></td>
                                    <td><span class="badge bg-soft-info text-info"><?= strtoupper($item['status']) ?></span></td>
                                    <td><?= $item['administered_date'] ? date('d M Y', strtotime($item['administered_date'])) : '-' ?></td>
                                    <td><?= htmlspecialchars($item['notes']) ?></td>
                                    <td class="text-end">
                                        <?php if (in_array(user()['role'], ['super_admin', 'admin', 'midwife'], true) && $item['status'] !== 'completed'): ?>
                                            <a href="<?= url('?page=immunizations-complete&id=' . $item['id']) ?>" class="btn btn-sm btn-outline-primary">Tandai Selesai</a>
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
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
