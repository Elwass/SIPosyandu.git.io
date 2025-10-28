<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><?= $resident ? 'Edit' : 'Tambah' ?> Warga</h2>
        <a href="?page=residents" class="btn btn-outline-secondary">Kembali</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= $resident ? '?page=residents-update' : '?page=residents-store' ?>">
                <?php if ($resident): ?>
                    <input type="hidden" name="id" value="<?= $resident['id'] ?>">
                <?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($resident['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nik" class="form-control" value="<?= htmlspecialchars($resident['nik'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. KK</label>
                        <input type="text" name="family_number" class="form-control" value="<?= htmlspecialchars($resident['family_number'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($resident['phone'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control" required><?= htmlspecialchars($resident['address'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-control" value="<?= htmlspecialchars($resident['birth_date'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="gender" class="form-select" required>
                            <option value="male" <?= isset($resident['gender']) && $resident['gender'] === 'male' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="female" <?= isset($resident['gender']) && $resident['gender'] === 'female' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select" required>
                            <option value="pregnant" <?= isset($resident['category']) && $resident['category'] === 'pregnant' ? 'selected' : '' ?>>Ibu Hamil</option>
                            <option value="toddler" <?= isset($resident['category']) && $resident['category'] === 'toddler' ? 'selected' : '' ?>>Balita</option>
                            <option value="elderly" <?= isset($resident['category']) && $resident['category'] === 'elderly' ? 'selected' : '' ?>>Lansia</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button class="btn btn-success" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
