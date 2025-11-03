<?php
session_start();

require_once __DIR__ . '/Helpers.php';

date_default_timezone_set(config('app.timezone', 'UTC'));

require_once __DIR__ . '/Database.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/Controllers/' . $class . '.php',
        __DIR__ . '/Models/' . $class . '.php',
        __DIR__ . '/Libraries/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});
