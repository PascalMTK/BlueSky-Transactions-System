<?php

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__ . '/../bluesky-transactions/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/../bluesky-transactions/vendor/autoload.php';

$app = require_once __DIR__ . '/../bluesky-transactions/bootstrap/app.php';

$app->handleRequest(\Illuminate\Http\Request::capture());
