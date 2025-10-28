<?php
$config = require __DIR__ . '/../../config.php';
$baseUrl = $config['app']['base_url'];
$appName = $config['app']['name'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appName) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/styles.css">
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<main class="main-content">
