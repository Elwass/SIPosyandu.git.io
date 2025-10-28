<?php
$config = require __DIR__ . '/../../config.php';
$baseUrl = $config['app']['base_url'];
$currentUser = user();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $baseUrl ?>">SI Posyandu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if ($currentUser): ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>?page=dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>?page=residents">Data Warga</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>?page=measurements">Penimbangan</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>?page=immunizations">Imunisasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>?page=reminders">Reminder</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>?page=reports">Laporan</a></li>
                    <?php if (in_array($currentUser['role'], ['super_admin'], true)): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>?page=users">Pengguna</a></li>
                    <?php endif; ?>
                </ul>
                <span class="navbar-text me-3">
                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentUser['role']))) ?>
                </span>
                <a class="btn btn-outline-light" href="<?= $baseUrl ?>?page=logout">Keluar</a>
            <?php else: ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= $baseUrl ?>?page=login">Masuk</a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>
