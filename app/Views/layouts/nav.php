<?php
$currentUser = user();
$currentPage = $_GET['page'] ?? ($currentUser ? 'dashboard' : 'landing');
$baseLandingUrl = url('?page=landing');
?>
<header class="site-header">
    <nav class="navbar navbar-expand-lg site-navbar">
        <div class="container">
            <a class="navbar-brand site-brand" href="<?= $currentUser ? url('?page=dashboard') : $baseLandingUrl ?>">
                <span class="brand-badge">SI</span>
                <span class="brand-text">Posyandu</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <?php if ($currentUser): ?>
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= url('?page=dashboard') ?>">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'residents' ? 'active' : '' ?>" href="<?= url('?page=residents') ?>">Data Warga</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'measurements' ? 'active' : '' ?>" href="<?= url('?page=measurements') ?>">Penimbangan</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'immunizations' ? 'active' : '' ?>" href="<?= url('?page=immunizations') ?>">Imunisasi</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'reminders' ? 'active' : '' ?>" href="<?= url('?page=reminders') ?>">Reminder</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>" href="<?= url('?page=reports') ?>">Laporan</a></li>
                        <?php if (in_array($currentUser['role'], ['super_admin'], true)): ?>
                            <li class="nav-item"><a class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>" href="<?= url('?page=users') ?>">Pengguna</a></li>
                        <?php endif; ?>
                        <li class="nav-item nav-item--profile">
                            <span class="badge rounded-pill bg-soft-primary text-primary me-lg-3">
                                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentUser['role']))) ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-primary btn-sm" href="<?= url('?page=logout') ?>">Keluar</a>
                        </li>
                    </ul>
                <?php else: ?>
                    <ul class="navbar-nav ms-auto align-items-lg-center public-nav">
                        <li class="nav-item"><a class="nav-link" href="<?= $baseLandingUrl ?>#hero">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $baseLandingUrl ?>#features">Fitur</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $baseLandingUrl ?>#services">Layanan</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= $baseLandingUrl ?>#contact">Kontak</a></li>
                        <li class="nav-item mt-3 mt-lg-0 ms-lg-3">
                            <a class="btn btn-primary" href="<?= url('?page=login') ?>">Masuk Sistem</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>
