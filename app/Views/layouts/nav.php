<?php
$currentUser = user();
$currentPage = $_GET['page'] ?? ($currentUser ? 'dashboard' : 'landing');
$baseLandingUrl = url('?page=landing');
$isAdminOrSuper = $currentUser && in_array($currentUser['role'], ['admin', 'super_admin'], true);
$layananPages = ['measurements', 'immunizations', 'reminders'];
$isLayananActive = in_array($currentPage, $layananPages, true);
$isMasterActive = $isAdminOrSuper && (
    $currentPage === 'medicines'
    || str_starts_with($currentPage, 'admin-medicines')
    || $currentPage === 'users'
);
$reportPages = ['reports', 'fulfillment-orders'];
$isReportActive = in_array($currentPage, $reportPages, true);
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
                        <?php if ($currentUser['role'] === 'pasien'): ?>
                            <li class="nav-item"><a class="nav-link <?= $currentPage === 'patient-dashboard' ? 'active' : '' ?>" href="<?= url('?page=patient-dashboard') ?>">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPage, 'recommendation') ? 'active' : '' ?>" href="<?= url('?page=recommendations') ?>">Rekomendasi</a></li>
                            <li class="nav-item"><a class="nav-link <?= $currentPage === 'fulfillment-orders' ? 'active' : '' ?>" href="<?= url('?page=fulfillment-orders') ?>">Pemenuhan</a></li>
                            <li class="nav-item"><a class="nav-link <?= $currentPage === 'patient-profile' ? 'active' : '' ?>" href="<?= url('?page=patient-profile') ?>">Profil & BPJS</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= url('?page=dashboard') ?>">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link <?= $currentPage === 'residents' ? 'active' : '' ?>" href="<?= url('?page=residents') ?>">Data Warga</a></li>
                            <li class="nav-item"><a class="nav-link <?= $currentPage === 'recommendations' || str_starts_with($currentPage, 'admin-recommendations') ? 'active' : '' ?>" href="<?= url('?page=recommendations') ?>">Rekomendasi</a></li>
                            <li class="nav-item custom-dropdown <?= $isLayananActive ? 'active open' : '' ?>">
                                <a href="#" class="nav-link dropdown-toggle" data-dropdown-toggle="layanan">Layanan</a>
                                <ul class="dropdown-menu custom-dropdown-menu" data-dropdown-menu="layanan">
                                    <li><a class="dropdown-item <?= $currentPage === 'measurements' ? 'active' : '' ?>" href="<?= url('?page=measurements') ?>">Penimbangan</a></li>
                                    <li><a class="dropdown-item <?= $currentPage === 'immunizations' ? 'active' : '' ?>" href="<?= url('?page=immunizations') ?>">Imunisasi</a></li>
                                    <li><a class="dropdown-item <?= $currentPage === 'reminders' ? 'active' : '' ?>" href="<?= url('?page=reminders') ?>">Reminder</a></li>
                                </ul>
                            </li>
                            <?php if ($isAdminOrSuper): ?>
                                <li class="nav-item custom-dropdown <?= $isMasterActive ? 'active open' : '' ?>">
                                    <a href="#" class="nav-link dropdown-toggle" data-dropdown-toggle="master">Master</a>
                                    <ul class="dropdown-menu custom-dropdown-menu" data-dropdown-menu="master">
                                        <li><a class="dropdown-item <?= $currentPage === 'medicines' || str_starts_with($currentPage, 'admin-medicines') ? 'active' : '' ?>" href="<?= url('?page=medicines') ?>">Obat</a></li>
                                        <li><a class="dropdown-item <?= $currentPage === 'users' ? 'active' : '' ?>" href="<?= url('?page=users') ?>">Pengguna</a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item custom-dropdown <?= $isReportActive ? 'active open' : '' ?>">
                                <a href="#" class="nav-link dropdown-toggle" data-dropdown-toggle="report">Laporan</a>
                                <ul class="dropdown-menu custom-dropdown-menu" data-dropdown-menu="report">
                                    <li><a class="dropdown-item <?= $currentPage === 'reports' ? 'active' : '' ?>" href="<?= url('?page=reports') ?>">Laporan</a></li>
                                    <li><a class="dropdown-item <?= $currentPage === 'fulfillment-orders' ? 'active' : '' ?>" href="<?= url('?page=fulfillment-orders') ?>">Pemenuhan</a></li>
                                </ul>
                            </li>
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
                            <a class="btn btn-outline-primary" href="<?= url('?page=login') ?>">Masuk Petugas</a>
                        </li>
                        <li class="nav-item mt-2 mt-lg-0 ms-lg-2">
                            <a class="btn btn-primary" href="<?= url('?page=patient-register') ?>">Daftar Pasien</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>
<style>
    .custom-dropdown {
        position: relative;
    }
    .custom-dropdown > .nav-link.dropdown-toggle::after {
        content: '\25BE';
        font-size: 0.75rem;
        margin-left: 0.35rem;
    }
    .custom-dropdown .custom-dropdown-menu {
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 200px;
        background: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        padding: 0.5rem 0;
        display: none;
        z-index: 1050;
    }
    .custom-dropdown.open > .custom-dropdown-menu,
    .custom-dropdown:hover > .custom-dropdown-menu,
    .custom-dropdown:focus-within > .custom-dropdown-menu {
        display: block;
    }
    .custom-dropdown .dropdown-item.active,
    .custom-dropdown.active > .nav-link {
        font-weight: 600;
        color: var(--bs-primary) !important;
    }
    .custom-dropdown .dropdown-item {
        padding: 0.45rem 1rem;
    }
    .custom-dropdown .dropdown-item:hover {
        background-color: rgba(13, 110, 253, 0.08);
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdowns = document.querySelectorAll('.custom-dropdown');

        function closeAll() {
            dropdowns.forEach(drop => drop.classList.remove('open'));
        }

        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('[data-dropdown-toggle]');
            toggle?.addEventListener('click', function (e) {
                e.preventDefault();
                const isOpen = dropdown.classList.contains('open');
                closeAll();
                if (!isOpen) {
                    dropdown.classList.add('open');
                }
            });
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.custom-dropdown')) {
                closeAll();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAll();
            }
        });
    });
</script>
