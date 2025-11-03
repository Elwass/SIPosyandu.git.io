<?php include __DIR__ . '/../layouts/header.php'; ?>
<section class="section section--error">
    <div class="container text-center">
        <div class="error-illustration">404</div>
        <h2 class="error-title">Halaman Tidak Ditemukan</h2>
        <p class="error-subtitle">Halaman yang Anda cari tidak tersedia atau sudah dipindahkan.</p>
        <a href="<?= url(is_logged_in() ? '?page=dashboard' : '?page=landing') ?>" class="btn btn-primary">Kembali</a>
    </div>
</section>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
