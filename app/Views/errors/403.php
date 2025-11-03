<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--error">
    <div class="container text-center">
        <div class="error-illustration">403</div>
        <h2 class="error-title">Akses Ditolak</h2>
        <p class="error-subtitle">Anda tidak memiliki hak untuk mengakses halaman ini. Silakan kembali ke dashboard atau hubungi admin.</p>
        <a href="<?= url('?page=dashboard') ?>" class="btn btn-primary">Kembali ke Dashboard</a>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
