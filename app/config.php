<?php
return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'si_posyandu',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        // Biarkan kosong untuk auto-detect berdasarkan lokasi index.php,
        // atau isi dengan URL absolut/path khusus (mis. http://localhost/si_posyandu/public)
        'base_url' => getenv('APP_BASE_URL') ?: '',
        'name' => 'SI Posyandu',
        'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Jakarta'
    ]
];
