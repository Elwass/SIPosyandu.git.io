<?php
$appName = config('app.name', 'SI Posyandu');
$isAuthenticated = is_logged_in();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Posyandu berbasis web untuk pencatatan data warga, penimbangan, imunisasi, dan laporan gizi.">
    <title><?= htmlspecialchars($appName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
</head>
<body class="app-body <?= $isAuthenticated ? 'app-body--authenticated' : 'app-body--public' ?>">
<?php include __DIR__ . '/nav.php'; ?>
<main class="main-content">
