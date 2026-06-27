<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Force writable paths for serverless manifests
$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

// Bind dynamic serverless temporary instances
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
