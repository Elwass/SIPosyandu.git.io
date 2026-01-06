<?php
$currentUser = user();
$currentPage = $_GET['page'] ?? ($currentUser ? 'dashboard' : 'landing');
$baseLandingUrl = url('?page=landing');
$isAdminOrSuper = $currentUser && in_array($currentUser['role'], ['admin', 'super_admin'], true);
$layananPages = ['measurements', 'immunizations', 'reminders'];
$reportPages = ['reports', 'fulfillment-orders'];
$isLayananActive = in_array($currentPage, $layananPages, true);
$isMasterActive = $isAdminOrSuper && (
    $currentPage === 'medicines'
    || str_starts_with($currentPage, 'admin-medicines')
    || $currentPage === 'users'
);
$isReportActive = in_array($currentPage, $reportPages, true);
?>

<?php if ($currentUser && $currentUser['role'] !== 'pasien'): ?>
    <div class="admin-layout" data-sidebar>
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-label="Toggle sidebar">&#9776;</button>
                <a class="sidebar-brand" href="<?= url('?page=dashboard') ?>">
                    <span class="brand-badge">SI</span>
                    <span class="brand-text">Posyandu</span>
                </a>
            </div>
            <nav class="sidebar-menu">
                <a class="sidebar-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= url('?page=dashboard') ?>">
                    <span class="icon">🏠</span><span class="label">Dashboard</span>
                </a>
                <a class="sidebar-link <?= $currentPage === 'residents' ? 'active' : '' ?>" href="<?= url('?page=residents') ?>">
                    <span class="icon">🧑‍🤝‍🧑</span><span class="label">Data Warga</span>
                </a>
                <a class="sidebar-link <?= $currentPage === 'recommendations' || str_starts_with($currentPage, 'admin-recommendations') ? 'active' : '' ?>" href="<?= url('?page=recommendations') ?>">
                    <span class="icon">💊</span><span class="label">Rekomendasi</span>
                </a>

                <div class="sidebar-dropdown <?= $isLayananActive ? 'open active' : '' ?>" data-dropdown="layanan">
                    <button class="sidebar-dropdown-toggle" type="button">
                        <span class="icon">🧭</span><span class="label">Layanan</span>
                        <span class="caret">▾</span>
                    </button>
                    <div class="sidebar-dropdown-menu" data-menu="layanan">
                        <a class="sidebar-sublink <?= $currentPage === 'measurements' ? 'active' : '' ?>" href="<?= url('?page=measurements') ?>">Penimbangan</a>
                        <a class="sidebar-sublink <?= $currentPage === 'immunizations' ? 'active' : '' ?>" href="<?= url('?page=immunizations') ?>">Imunisasi</a>
                        <a class="sidebar-sublink <?= $currentPage === 'reminders' ? 'active' : '' ?>" href="<?= url('?page=reminders') ?>">Reminder</a>
                    </div>
                </div>

                <?php if ($isAdminOrSuper): ?>
                    <div class="sidebar-dropdown <?= $isMasterActive ? 'open active' : '' ?>" data-dropdown="master">
                        <button class="sidebar-dropdown-toggle" type="button">
                            <span class="icon">🗂️</span><span class="label">Master</span>
                            <span class="caret">▾</span>
                        </button>
                        <div class="sidebar-dropdown-menu" data-menu="master">
                            <a class="sidebar-sublink <?= $currentPage === 'medicines' || str_starts_with($currentPage, 'admin-medicines') ? 'active' : '' ?>" href="<?= url('?page=medicines') ?>">Obat</a>
                            <a class="sidebar-sublink <?= $currentPage === 'users' ? 'active' : '' ?>" href="<?= url('?page=users') ?>">Pengguna</a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="sidebar-dropdown <?= $isReportActive ? 'open active' : '' ?>" data-dropdown="report">
                    <button class="sidebar-dropdown-toggle" type="button">
                        <span class="icon">📑</span><span class="label">Laporan</span>
                        <span class="caret">▾</span>
                    </button>
                    <div class="sidebar-dropdown-menu" data-menu="report">
                        <a class="sidebar-sublink <?= $currentPage === 'reports' ? 'active' : '' ?>" href="<?= url('?page=reports') ?>">Laporan</a>
                        <a class="sidebar-sublink <?= $currentPage === 'fulfillment-orders' ? 'active' : '' ?>" href="<?= url('?page=fulfillment-orders') ?>">Pemenuhan</a>
                    </div>
                </div>
            </nav>
        </aside>
        <div class="admin-main">
            <div class="admin-topbar">
                <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-label="Toggle sidebar">&#9776;</button>
                <div class="admin-topbar__right">
                    <span class="badge rounded-pill bg-soft-primary text-primary">
                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentUser['role']))) ?>
                    </span>
                    <a class="btn btn-outline-primary btn-sm" href="<?= url('?page=logout') ?>">Keluar</a>
                </div>
            </div>
<?php else: ?>
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
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'patient-dashboard' ? 'active' : '' ?>" href="<?= url('?page=patient-dashboard') ?>">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link <?= str_starts_with($currentPage, 'recommendation') ? 'active' : '' ?>" href="<?= url('?page=recommendations') ?>">Rekomendasi</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'fulfillment-orders' ? 'active' : '' ?>" href="<?= url('?page=fulfillment-orders') ?>">Pemenuhan</a></li>
                        <li class="nav-item"><a class="nav-link <?= $currentPage === 'patient-profile' ? 'active' : '' ?>" href="<?= url('?page=patient-profile') ?>">Profil & BPJS</a></li>
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
<?php endif; ?>

<style>
    .app-body--admin {
        background: #f8fafc;
    }
    .admin-layout {
        display: flex;
        min-height: 100vh;
    }
    .admin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 260px;
        background: #0d6efd;
        color: #fff;
        padding: 1rem 0.75rem;
        display: flex;
        flex-direction: column;
        box-shadow: 4px 0 18px rgba(0,0,0,0.08);
        z-index: 1040;
        transition: width 0.2s ease, transform 0.2s ease;
    }
    .sidebar-header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.25rem;
    }
    .sidebar-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        letter-spacing: 0.2px;
    }
    .sidebar-toggle {
        background: rgba(255,255,255,0.12);
        border: none;
        color: #fff;
        width: 40px;
        height: 40px;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        overflow-y: auto;
        padding-right: 0.25rem;
    }
    .sidebar-link,
    .sidebar-dropdown-toggle {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.65rem 0.85rem;
        color: #e7efff;
        border-radius: 0.7rem;
        text-decoration: none;
        border: none;
        width: 100%;
        background: transparent;
        cursor: pointer;
        transition: background 0.15s ease, color 0.15s ease;
    }
    .sidebar-link.active,
    .sidebar-dropdown.active > .sidebar-dropdown-toggle,
    .sidebar-link:hover,
    .sidebar-dropdown-toggle:hover {
        background: rgba(255,255,255,0.16);
        color: #fff;
    }
    .sidebar-link .icon,
    .sidebar-dropdown-toggle .icon {
        font-size: 1.1rem;
        width: 1.25rem;
        text-align: center;
    }
    .sidebar-link .label,
    .sidebar-dropdown-toggle .label {
        flex: 1;
        white-space: nowrap;
    }
    .sidebar-dropdown {
        background: rgba(255,255,255,0.06);
        border-radius: 0.75rem;
        overflow: hidden;
    }
    .sidebar-dropdown-menu {
        max-height: 0;
        overflow: hidden;
        background: rgba(255,255,255,0.05);
        display: flex;
        flex-direction: column;
        transition: max-height 0.25s ease;
    }
    .sidebar-dropdown.open .sidebar-dropdown-menu {
        max-height: 400px;
    }
    .sidebar-sublink {
        display: block;
        padding: 0.55rem 1rem 0.55rem 3rem;
        color: #dbe9ff;
        text-decoration: none;
        transition: background 0.15s ease, color 0.15s ease;
    }
    .sidebar-sublink.active,
    .sidebar-sublink:hover {
        background: rgba(255,255,255,0.14);
        color: #fff;
    }
    .sidebar-dropdown .caret {
        font-size: 0.8rem;
    }
    .admin-topbar {
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1rem;
        background: #ffffff;
        border-bottom: 1px solid #e9eef5;
        position: sticky;
        top: 0;
        z-index: 5;
    }
    .admin-topbar__right {
        display: inline-flex;
        gap: 0.75rem;
        align-items: center;
    }
    .admin-main {
        flex: 1;
        margin-left: 260px;
        min-height: 100vh;
        background: #f8fafc;
        transition: margin-left 0.2s ease;
    }
    .app-body--admin.sidebar-collapsed .admin-sidebar {
        width: 72px;
    }
    .app-body--admin.sidebar-collapsed .admin-main {
        margin-left: 72px;
    }
    .app-body--admin.sidebar-collapsed .sidebar-link .label,
    .app-body--admin.sidebar-collapsed .sidebar-dropdown-toggle .label,
    .app-body--admin.sidebar-collapsed .sidebar-sublink {
        display: none;
    }
    .app-body--admin.sidebar-collapsed .sidebar-dropdown-menu {
        position: absolute;
        left: 72px;
        right: auto;
        background: #0d6efd;
        padding: 0.4rem 0.2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .admin-topbar .sidebar-toggle {
        color: #0d6efd;
        background: rgba(13,110,253,0.08);
    }
    .main-content {
        transition: margin-left 0.2s ease;
    }
    @media (max-width: 991px) {
        .admin-sidebar {
            transform: translateX(-100%);
        }
        .admin-layout.sidebar-open .admin-sidebar,
        .app-body--admin.sidebar-collapsed .admin-sidebar {
            transform: translateX(0);
        }
        .admin-main,
        .main-content {
            margin-left: 0 !important;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const body = document.body;
        const layout = document.querySelector('[data-sidebar]');
        if (!layout) {
            return;
        }

        const collapseKey = 'adminSidebarCollapsed';
        const initialCollapsed = localStorage.getItem(collapseKey) === 'true';
        if (initialCollapsed) {
            body.classList.add('sidebar-collapsed');
        }

        const toggleSidebar = () => {
            const collapsed = body.classList.toggle('sidebar-collapsed');
            localStorage.setItem(collapseKey, collapsed ? 'true' : 'false');
        };

        document.querySelectorAll('[data-sidebar-toggle]').forEach(btn => {
            btn.addEventListener('click', function () {
                toggleSidebar();
                layout.classList.add('sidebar-open');
                if (window.innerWidth < 992) {
                    setTimeout(() => layout.classList.remove('sidebar-open'), 250);
                }
            });
        });

        const dropdowns = document.querySelectorAll('.sidebar-dropdown');
        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.sidebar-dropdown-toggle');
            const menu = dropdown.querySelector('.sidebar-dropdown-menu');
            const key = 'adminDropdown-' + (dropdown.dataset.dropdown || '');
            if (localStorage.getItem(key) === 'open' || dropdown.classList.contains('open')) {
                dropdown.classList.add('open');
                menu.style.maxHeight = menu.scrollHeight + 'px';
            }
            toggle?.addEventListener('click', function () {
                const isOpen = dropdown.classList.toggle('open');
                menu.style.maxHeight = isOpen ? menu.scrollHeight + 'px' : '0px';
                localStorage.setItem(key, isOpen ? 'open' : 'closed');
            });
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.admin-sidebar') && !e.target.closest('[data-sidebar-toggle]') && window.innerWidth < 992) {
                layout.classList.remove('sidebar-open');
            }
        });
    });
</script>
