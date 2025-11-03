<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section auth-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="auth-card shadow-lg">
                    <h3 class="auth-title text-center">Masuk ke SI Posyandu</h3>
                    <p class="auth-subtitle text-center">Gunakan akun Puskesmas, bidan, atau kader untuk melanjutkan.</p>
                    <?php if ($error = flash('error')): ?>
                        <div class="alert alert-danger alert-modern"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="post" action="<?= url('?page=login') ?>" class="auth-form">
                        <div class="form-floating mb-3">
                            <input type="email" name="email" id="email" class="form-control" placeholder="nama@puskesmas.go.id" required>
                            <label for="email">Email</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                            <label for="password">Password</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Masuk</button>
                    </form>
                    <p class="auth-help text-center">Butuh bantuan? Hubungi admin super untuk reset akun.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
