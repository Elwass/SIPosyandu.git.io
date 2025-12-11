<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="auth-card shadow-lg">
                    <h3 class="auth-title text-center">Daftar Akun Pasien</h3>
                    <p class="auth-subtitle text-center">Buat akun orang tua/ibu balita untuk memantau jadwal Posyandu.</p>

                    <?php if ($error = flash('error')): ?>
                        <div class="alert alert-danger alert-modern"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if ($success = flash('success')): ?>
                        <div class="alert alert-success alert-modern"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <form method="post" action="<?= url('?page=patient-register') ?>" class="auth-form">
                        <div class="form-floating mb-3">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nama lengkap" required>
                            <label for="name">Nama Lengkap</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" name="email" id="email" class="form-control" placeholder="email@contoh.com" required>
                            <label for="email">Email</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" required>
                            <label for="password">Kata Sandi</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi sandi" required>
                            <label for="password_confirmation">Ulangi Kata Sandi</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" name="no_bpjs" id="no_bpjs" class="form-control" placeholder="Nomor BPJS/KIS">
                            <label for="no_bpjs">Nomor BPJS/KIS (opsional)</label>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Status Kepesertaan</label>
                            <select name="status_bpjs" class="form-select">
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                                <option value="tidak_diketahui" selected>Belum Mengetahui</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Buat Akun Pasien</button>
                    </form>

                    <p class="auth-help text-center mt-3">Sudah punya akun? <a href="<?= url('?page=login') ?>">Masuk di sini</a>.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
