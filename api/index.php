<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Override serverless execution limitations dynamically at the core PHP level
ini_set('max_execution_time', 120);
ini_set('memory_limit', '512M');

if (!is_dir('/tmp/storage/framework/views')) {
    mkdir('/tmp/storage/framework/views', 0755, true);
}
if (!is_dir('/tmp/storage/framework/cache')) {
    mkdir('/tmp/storage/framework/cache', 0755, true);
}
if (!is_dir('/tmp/storage/framework/sessions')) {
    mkdir('/tmp/storage/framework/sessions', 0755, true);
}

$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';

$app->useStoragePath('/tmp/storage');
$app->instance('path.config_cache', '/tmp/config.php');
$app->instance('path.routes_cache', '/tmp/routes.php');
$app->instance('path.services_cache', '/tmp/services.php');
$app->instance('path.packages_cache', '/tmp/packages.php');

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
