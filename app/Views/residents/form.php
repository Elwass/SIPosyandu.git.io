<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--page">
    <div class="container">
        <div class="section-heading d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center">
            <div>
                <span class="section-eyebrow">Formulir Warga</span>
                <h2 class="section-title mb-0"><?= $resident ? 'Edit Data Warga' : 'Tambah Data Warga' ?></h2>
                <p class="section-subtitle">Pastikan data warga lengkap untuk mendukung pencatatan layanan Posyandu.</p>
            </div>
            <a href="<?= url('?page=residents') ?>" class="btn btn-outline-primary mt-3 mt-lg-0">Kembali ke daftar</a>
        </div>
        <div class="surface-card">
            <div class="surface-body">
                <form method="post" action="<?= $resident ? url('?page=residents-update') : url('?page=residents-store') ?>" class="form-grid">
                    <?php if ($resident): ?>
                        <input type="hidden" name="id" value="<?= $resident['id'] ?>">
                    <?php endif; ?>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control form-control-modern" value="<?= htmlspecialchars($resident['name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control form-control-modern" value="<?= htmlspecialchars($resident['nik'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. KK</label>
                            <input type="text" name="family_number" class="form-control form-control-modern" value="<?= htmlspecialchars($resident['family_number'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="phone" class="form-control form-control-modern" value="<?= htmlspecialchars($resident['phone'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" class="form-control form-control-modern" rows="3" required><?= htmlspecialchars($resident['address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="form-control form-control-modern" value="<?= htmlspecialchars($resident['birth_date'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender" class="form-select form-select-modern" required>
                                <option value="male" <?= isset($resident['gender']) && $resident['gender'] === 'male' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="female" <?= isset($resident['gender']) && $resident['gender'] === 'female' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kategori</label>
                            <select name="category" class="form-select form-select-modern" required>
                                <option value="pregnant" <?= isset($resident['category']) && $resident['category'] === 'pregnant' ? 'selected' : '' ?>>Ibu Hamil</option>
                                <option value="toddler" <?= isset($resident['category']) && $resident['category'] === 'toddler' ? 'selected' : '' ?>>Balita</option>
                                <option value="elderly" <?= isset($resident['category']) && $resident['category'] === 'elderly' ? 'selected' : '' ?>>Lansia</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions text-end mt-4">
                        <button class="btn btn-primary" type="submit">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
