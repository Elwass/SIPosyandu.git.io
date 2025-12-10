<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--dashboard">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Profil Pasien</span>
            <h2 class="section-title">Data Pribadi & BPJS</h2>
            <p class="section-subtitle">Perbarui informasi BPJS dan tambahkan balita yang Anda dampingi.</p>
        </div>

        <?php if ($message = flash('success')): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="surface-card h-100">
                    <div class="surface-header">
                        <h3>Data Akun</h3>
                    </div>
                    <div class="surface-body">
                        <p class="mb-1 fw-semibold">Nama</p>
                        <p><?= htmlspecialchars($patient['name']) ?></p>
                        <p class="mb-1 fw-semibold">Email</p>
                        <p><?= htmlspecialchars($patient['email']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="surface-card h-100">
                    <div class="surface-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Data BPJS</h3>
                    </div>
                    <div class="surface-body">
                        <form method="post" action="<?= url('?page=patient-bpjs-update') ?>">
                            <div class="mb-3">
                                <label class="form-label">Nomor BPJS/KIS</label>
                                <input type="text" name="no_bpjs" class="form-control" value="<?= htmlspecialchars($bpjs['no_bpjs'] ?? '') ?>" maxlength="25">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status BPJS</label>
                                <select name="status_bpjs" class="form-select">
                                    <?php $status = $bpjs['status_bpjs'] ?? 'tidak_diketahui'; ?>
                                    <option value="aktif" <?= $status === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="tidak_aktif" <?= $status === 'tidak_aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                                    <option value="tidak_diketahui" <?= $status === 'tidak_diketahui' ? 'selected' : '' ?>>Tidak Diketahui</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis BPJS</label>
                                <input type="text" name="jenis_bpjs" class="form-control" value="<?= htmlspecialchars($bpjs['jenis_bpjs'] ?? '') ?>" placeholder="PBI / Mandiri / PPU">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Faskes Tingkat 1</label>
                                <input type="text" name="faskes_tingkat_1" class="form-control" value="<?= htmlspecialchars($bpjs['faskes_tingkat_1'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Validasi</label>
                                <input type="date" name="tanggal_validasi" class="form-control" value="<?= htmlspecialchars($bpjs['tanggal_validasi'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan tambahan mengenai kepesertaan BPJS"><?= htmlspecialchars($bpjs['keterangan'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan BPJS</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="surface-card mt-4">
            <div class="surface-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Balita Anda</h3>
                <span class="badge rounded-pill bg-soft-primary text-primary"><?= count($children) ?> terdaftar</span>
            </div>
            <div class="surface-body">
                <?php if ($children): ?>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal Lahir</th>
                                    <th>NIK</th>
                                    <th>Status Gizi Terakhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($children as $child): ?>
                                    <?php $latest = $latestMeasurements[$child['id']] ?? null; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($child['name']) ?></td>
                                        <td><?= date('d M Y', strtotime($child['birth_date'])) ?></td>
                                        <td><?= htmlspecialchars($child['nik']) ?></td>
                                        <td>
                                            <?php if ($latest): ?>
                                                <span class="badge bg-soft-success text-success"><?= htmlspecialchars(str_replace('_', ' ', $latest['nutritional_status'])) ?></span>
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
                    <p class="text-muted">Belum ada balita terhubung.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="surface-card mt-4">
            <div class="surface-header">
                <h3>Tambah Balita</h3>
                <p class="text-muted mb-0">Isikan data balita yang ingin Anda hubungkan ke akun ini. Kategori otomatis diset sebagai "toddler".</p>
            </div>
            <div class="surface-body">
                <form method="post" action="<?= url('?page=patient-child-store') ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Balita</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. KK</label>
                            <input type="text" name="family_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Alamat</label>
                            <input type="text" name="address" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender" class="form-select" required>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Simpan Balita</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
