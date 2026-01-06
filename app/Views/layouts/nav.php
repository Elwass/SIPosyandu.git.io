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
$pageToLabel = [
    'dashboard' => 'Dashboard',
    'residents' => 'Data Warga',
    'recommendations' => 'Rekomendasi',
    'measurements' => 'Penimbangan',
    'immunizations' => 'Imunisasi',
    'reminders' => 'Reminder',
    'medicines' => 'Obat',
    'users' => 'Pengguna',
    'reports' => 'Laporan',
    'fulfillment-orders' => 'Pemenuhan',
];
?>

<?php if ($currentUser && $currentUser['role'] !== 'pasien'): ?>
    <div class="admin-layout" data-sidebar>
        <aside class="sidebar" id="adminSidebar">
            <div class="sidebar__top">
                <button class="sidebar__toggle" type="button" data-sidebar-toggle aria-label="Toggle sidebar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <a class="sidebar__brand" href="<?= url('?page=dashboard') ?>">
                    <span class="brand__mark">SI</span>
                    <div class="brand__text">
                        <span class="brand__title">Posyandu</span>
                        <small class="brand__subtitle">Panel Admin</small>
                    </div>
                </a>
            </div>
            <nav class="sidebar__menu">
                <a class="nav-item <?= $currentPage === 'dashboard' ? 'nav-item--active' : '' ?>" href="<?= url('?page=dashboard') ?>" data-label="Dashboard">
                    <span class="nav-item__icon"> 
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-6 9 6"/><path d="M9 22V12h6v10"/><path d="M21 10v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V10"/>
                        </svg>
                    </span>
                    <span class="nav-item__label">Dashboard</span>
                </a>
                <a class="nav-item <?= $currentPage === 'residents' ? 'nav-item--active' : '' ?>" href="<?= url('?page=residents') ?>" data-label="Data Warga">
                    <span class="nav-item__icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="8.5" cy="7" r="4"/>
                            <path d="M20 8v6"/><path d="M23 11h-6"/>
                        </svg>
                    </span>
                    <span class="nav-item__label">Data Warga</span>
                </a>
                <a class="nav-item <?= ($currentPage === 'recommendations' || str_starts_with($currentPage, 'admin-recommendations')) ? 'nav-item--active' : '' ?>" href="<?= url('?page=recommendations') ?>" data-label="Rekomendasi">
                    <span class="nav-item__icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 21h10"/><path d="M12 17v4"/><path d="M7 9h10l1 7H6z"/><path d="M12 4a2 2 0 1 1 4 0v2H8V6a2 2 0 1 1 4 0"/>
                        </svg>
                    </span>
                    <span class="nav-item__label">Rekomendasi</span>
                </a>

                <div class="nav-group <?= $isLayananActive ? 'nav-group--open nav-group--active' : '' ?>" data-dropdown="layanan">
                    <button class="nav-group__toggle" type="button" data-label="Layanan">
                        <span class="nav-item__icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                            </svg>
                        </span>
                        <span class="nav-item__label">Layanan</span>
                        <span class="nav-group__caret" aria-hidden="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </span>
                    </button>
                    <div class="nav-submenu" data-menu="layanan">
                        <a class="nav-sublink <?= $currentPage === 'measurements' ? 'nav-sublink--active' : '' ?>" href="<?= url('?page=measurements') ?>">Penimbangan</a>
                        <a class="nav-sublink <?= $currentPage === 'immunizations' ? 'nav-sublink--active' : '' ?>" href="<?= url('?page=immunizations') ?>">Imunisasi</a>
                        <a class="nav-sublink <?= $currentPage === 'reminders' ? 'nav-sublink--active' : '' ?>" href="<?= url('?page=reminders') ?>">Reminder</a>
                    </div>
                </div>

                <?php if ($isAdminOrSuper): ?>
                    <div class="nav-group <?= $isMasterActive ? 'nav-group--open nav-group--active' : '' ?>" data-dropdown="master">
                        <button class="nav-group__toggle" type="button" data-label="Master">
                            <span class="nav-item__icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="6" rx="1"/><rect x="3" y="14" width="18" height="6" rx="1"/><path d="M7 4v6"/><path d="M7 14v6"/>
                                </svg>
                            </span>
                            <span class="nav-item__label">Master</span>
                            <span class="nav-group__caret" aria-hidden="true">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </span>
                        </button>
                        <div class="nav-submenu" data-menu="master">
                            <a class="nav-sublink <?= $currentPage === 'medicines' || str_starts_with($currentPage, 'admin-medicines') ? 'nav-sublink--active' : '' ?>" href="<?= url('?page=medicines') ?>">Obat</a>
                            <a class="nav-sublink <?= $currentPage === 'users' ? 'nav-sublink--active' : '' ?>" href="<?= url('?page=users') ?>">Pengguna</a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="nav-group <?= $isReportActive ? 'nav-group--open nav-group--active' : '' ?>" data-dropdown="report">
                    <button class="nav-group__toggle" type="button" data-label="Laporan">
                        <span class="nav-item__icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16v16H4z"/><path d="M9 4v16"/><path d="M4 9h5"/>
                            </svg>
                        </span>
                        <span class="nav-item__label">Laporan</span>
                        <span class="nav-group__caret" aria-hidden="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </span>
                    </button>
                    <div class="nav-submenu" data-menu="report">
                        <a class="nav-sublink <?= $currentPage === 'reports' ? 'nav-sublink--active' : '' ?>" href="<?= url('?page=reports') ?>">Laporan</a>
                        <a class="nav-sublink <?= $currentPage === 'fulfillment-orders' ? 'nav-sublink--active' : '' ?>" href="<?= url('?page=fulfillment-orders') ?>">Pemenuhan</a>
                    </div>
                </div>
            </nav>
        </aside>
        <div class="admin-main">
            <div class="admin-topbar">
                <div class="admin-topbar__left">
                    <button class="sidebar__toggle" type="button" data-sidebar-toggle aria-label="Toggle sidebar">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <div class="admin-topbar__meta">
                        <span class="app-title">SI Posyandu</span>
                        <span class="app-subtitle">Panel Admin</span>
                    </div>
                </div>
                <div class="admin-topbar__right">
                    <span class="role-badge">
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
    :root {
        --sidebar-bg: #0f172a;
        --sidebar-bg-alt: #111827;
        --sidebar-accent: #1f3a8a;
        --sidebar-hover: #1e293b;
        --sidebar-text: #e2e8f0;
        --sidebar-text-muted: #94a3b8;
        --sidebar-border: #1f2937;
        --sidebar-active: rgba(255,255,255,0.08);
        --body-font: "Inter", "Segoe UI", Arial, Helvetica, sans-serif;
    }

    body {
        font-family: var(--body-font);
        font-size: 14.5px;
    }

    .app-body--admin {
        background: #f8fafc;
    }

    .admin-layout {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        position: fixed;
        inset: 0 auto 0 0;
        width: 260px;
        background: var(--sidebar-bg);
        color: var(--sidebar-text);
        display: flex;
        flex-direction: column;
        padding: 1rem 0.75rem 1.25rem;
        box-shadow: 4px 0 22px rgba(0,0,0,0.18);
        z-index: 1040;
        transition: width 0.22s ease;
    }

    .sidebar__top {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 1.1rem;
        padding: 0 0.35rem;
    }

    .sidebar__brand {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        color: var(--sidebar-text);
        text-decoration: none;
    }

    .brand__mark {
        width: 38px;
        height: 38px;
        background: var(--sidebar-accent);
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .brand__text {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .brand__title {
        font-size: 15px;
        font-weight: 700;
    }

    .brand__subtitle {
        font-size: 12px;
        color: var(--sidebar-text-muted);
    }

    .sidebar__toggle {
        background: transparent;
        border: 1px solid var(--sidebar-border);
        color: var(--sidebar-text);
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .sidebar__toggle:hover {
        background: var(--sidebar-hover);
        border-color: var(--sidebar-accent);
    }

    .sidebar__menu {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
        overflow-y: auto;
        padding-right: 0.15rem;
    }

    .nav-item,
    .nav-group__toggle {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.7rem 0.85rem;
        color: var(--sidebar-text);
        text-decoration: none;
        border-radius: 10px;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        position: relative;
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }

    .nav-item:hover,
    .nav-group__toggle:hover {
        background: var(--sidebar-hover);
        border-color: var(--sidebar-border);
    }

    .nav-item--active {
        background: var(--sidebar-active);
        border-left: 3px solid var(--sidebar-accent);
        color: #fff;
    }

    .nav-item__icon {
        width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--sidebar-text);
    }

    .nav-item__label {
        flex: 1;
        text-align: left;
        font-weight: 500;
        letter-spacing: 0.1px;
    }

    .nav-group {
        border-radius: 12px;
        padding: 0.1rem 0.05rem;
    }

    .nav-group__toggle {
        width: 100%;
        border-radius: 10px;
    }

    .nav-group__caret {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.18s ease;
    }

    .nav-group--open .nav-group__caret {
        transform: rotate(180deg);
    }

    .nav-submenu {
        max-height: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        padding-left: 0.3rem;
        transition: max-height 0.22s ease;
    }

    .nav-group--open .nav-submenu {
        max-height: 500px;
    }

    .nav-group--active > .nav-group__toggle {
        background: var(--sidebar-active);
        border-left: 3px solid var(--sidebar-accent);
    }

    .nav-sublink {
        color: var(--sidebar-text-muted);
        padding: 0.45rem 0.95rem 0.45rem 2.4rem;
        text-decoration: none;
        border-radius: 8px;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .nav-sublink:hover {
        color: #fff;
        background: var(--sidebar-hover);
    }

    .nav-sublink--active {
        color: #fff;
        background: var(--sidebar-active);
        border-left: 3px solid var(--sidebar-accent);
    }

    .sidebar--collapsed {
        width: 76px;
    }

    .sidebar--collapsed .brand__text,
    .sidebar--collapsed .nav-item__label,
    .sidebar--collapsed .nav-group__caret,
    .sidebar--collapsed .nav-submenu,
    .sidebar--collapsed .brand__subtitle {
        display: none !important;
    }

    .sidebar--collapsed .nav-item,
    .sidebar--collapsed .nav-group__toggle {
        justify-content: center;
    }

    .sidebar--collapsed .nav-group--open .nav-submenu {
        display: none;
    }

    .sidebar--collapsed .nav-item:hover::after,
    .sidebar--collapsed .nav-group__toggle:hover::after {
        content: attr(data-label);
        position: absolute;
        left: 90%;
        top: 50%;
        transform: translateY(-50%);
        background: #0b1221;
        color: #f8fafc;
        padding: 0.35rem 0.6rem;
        border-radius: 8px;
        white-space: nowrap;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 2000;
        font-size: 13px;
        border: 1px solid #1f2937;
    }

    .admin-main {
        flex: 1;
        margin-left: 260px;
        min-height: 100vh;
        background: #f8fafc;
        transition: margin-left 0.22s ease;
    }

    .sidebar--collapsed + .admin-main {
        margin-left: 76px;
    }

    .admin-topbar {
        height: 68px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.25rem;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .admin-topbar__left {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }

    .admin-topbar__meta {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .app-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 16px;
    }

    .app-subtitle {
        font-size: 12px;
        color: #64748b;
    }

    .admin-topbar__right {
        display: inline-flex;
        gap: 0.75rem;
        align-items: center;
    }

    .role-badge {
        background: #e0e7ff;
        color: #1e3a8a;
        padding: 0.35rem 0.6rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: 0.2px;
    }

    .main-content {
        transition: margin-left 0.22s ease;
    }

    @media (max-width: 991px) {
        .sidebar {
            transform: translateX(-100%);
            position: fixed;
        }
        .admin-layout.sidebar-open .sidebar {
            transform: translateX(0);
        }
        .sidebar--collapsed {
            transform: translateX(0);
        }
        .admin-main,
        .main-content,
        .sidebar--collapsed + .admin-main {
            margin-left: 0 !important;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const layout = document.querySelector('[data-sidebar]');
        const sidebar = document.getElementById('adminSidebar');
        const adminMain = document.querySelector('.admin-main');
        if (!layout || !sidebar) return;

        const collapseKey = 'adminSidebarCollapsedV2';

        const applyOffsets = () => {
            if (window.innerWidth <= 991) {
                if (adminMain) adminMain.style.marginLeft = '0';
                return;
            }
            if (adminMain) {
                adminMain.style.marginLeft = sidebar.classList.contains('sidebar--collapsed') ? '76px' : '260px';
            }
        };

        if (localStorage.getItem(collapseKey) === 'true') {
            sidebar.classList.add('sidebar--collapsed');
        }
        applyOffsets();

        const toggleSidebar = () => {
            sidebar.classList.toggle('sidebar--collapsed');
            const isCollapsed = sidebar.classList.contains('sidebar--collapsed');
            localStorage.setItem(collapseKey, isCollapsed ? 'true' : 'false');
            applyOffsets();
        };

        document.querySelectorAll('[data-sidebar-toggle]').forEach(btn => {
            btn.addEventListener('click', () => {
                toggleSidebar();
                layout.classList.add('sidebar-open');
                if (window.innerWidth < 992) {
                    setTimeout(() => layout.classList.remove('sidebar-open'), 220);
                }
            });
        });

        const dropdowns = document.querySelectorAll('.nav-group');
        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.nav-group__toggle');
            const menu = dropdown.querySelector('.nav-submenu');
            const key = 'adminDropdown-' + (dropdown.dataset.dropdown || '');
            const hasActive = dropdown.querySelector('.nav-sublink--active');
            if (localStorage.getItem(key) === 'open' || dropdown.classList.contains('nav-group--open') || hasActive) {
                dropdown.classList.add('nav-group--open');
                if (hasActive) dropdown.classList.add('nav-group--active');
                if (menu) menu.style.maxHeight = menu.scrollHeight + 'px';
            }
            toggle?.addEventListener('click', function () {
                const isOpen = dropdown.classList.toggle('nav-group--open');
                if (menu) menu.style.maxHeight = isOpen ? menu.scrollHeight + 'px' : '0px';
                localStorage.setItem(key, isOpen ? 'open' : 'closed');
            });
        });

        window.addEventListener('resize', applyOffsets);

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.sidebar') && !e.target.closest('[data-sidebar-toggle]') && window.innerWidth < 992) {
                layout.classList.remove('sidebar-open');
            }
        });
    });
</script>
