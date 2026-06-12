<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Vercel: filesystem is read-only — redirect writable paths to /tmp
|--------------------------------------------------------------------------
*/
$tmp = '/tmp/storage';

foreach ([
    "$tmp/app/public",
    "$tmp/framework/cache/data",
    "$tmp/framework/sessions",
    "$tmp/framework/views",
    "$tmp/logs",
] as $dir) {
    is_dir($dir) || mkdir($dir, 0755, true);
}

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$app->useStoragePath($tmp);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
