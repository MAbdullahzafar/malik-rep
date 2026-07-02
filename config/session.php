<?php

use Illuminate\Support\Str;

return [

    // HARDCODED FIX: Stops reading the .env file value and forces secure memory-based cookie drivers
    'driver' => 'cookie',

    'lifetime' => 120,

    'expire_on_close' => false,

    'encrypt' => true,

    'files' => storage_path('framework/sessions'),

    'connection' => null,

    'table' => 'sessions',

    'store' => null,

    'lottery' => array(2, 100),

    'cookie' => Str::slug(env('APP_NAME', 'laravel'), '_').'_session',

    'path' => '/',

    'domain' => '.vercel.app',

    'secure' => true,

    'http_only' => true,

    'same_site' => 'lax',

    'partitioned' => false,

];
