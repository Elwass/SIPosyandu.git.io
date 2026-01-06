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
    ],
    'midtrans' => [
        // Isi dengan kredensial Midtrans Anda. Nilai placeholder ini harus diganti di lingkungan produksi.
        'server_key' => getenv('MIDTRANS_SERVER_KEY') ?: '__MIDTRANS_SERVER_KEY__',
        'client_key' => getenv('MIDTRANS_CLIENT_KEY') ?: '__MIDTRANS_CLIENT_KEY__',
        'merchant_id' => getenv('MIDTRANS_MERCHANT_ID') ?: '__MIDTRANS_MERCHANT_ID__',
        'is_production' => getenv('MIDTRANS_IS_PRODUCTION') === 'true'
    ]
];
