<?php
// config/config.php
return [
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'bakery_db',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    // used for signing cookies (DO NOT commit a real secret)
    'secret_key' => 'replace_with_a_long_random_secret_key',
    'app_url' => '/bakeryApp', // base URL if you deploy in a subfolder, e.g. /bakery_app/
    'cookie_secure' => false, // true on HTTPS
];
