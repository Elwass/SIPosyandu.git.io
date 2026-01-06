<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--dashboard">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Data Keluarga</span>
            <h2 class="section-title">Tambah Data Keluarga</h2>
            <p class="section-subtitle">Lengkapi data anggota keluarga yang akan dihubungkan ke akun Anda.</p>
        </div>

        <?php if ($message = flash('error')): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($message = flash('success')): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="surface-card">
            <div class="surface-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Formulir Keluarga</h3>
                <a href="<?= url('?page=patient-dashboard') ?>" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
            </div>
            <div class="surface-body">
                <form method="post" action="<?= url('?page=patient-family-store') ?>" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Kartu Keluarga</label>
                            <input type="text" name="family_number" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender" class="form-select" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kategori</label>
                            <select name="category" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="pregnant">Ibu Hamil</option>
                                <option value="toddler">Balita</option>
                                <option value="elderly">Lansia</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Simpan Data Keluarga</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
