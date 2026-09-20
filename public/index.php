<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// فحص وجود وضع الصيانة
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// استدعاء التحميل التلقائي لمكتبات Composer
require __DIR__.'/../vendor/autoload.php';

// تحميل إعدادات التطبيق وتحديد آلية التشغيل بحسب إصدار Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

if (is_object($app) && method_exists($app, 'handleRequest')) {
    // Laravel 11+
    $app->handleRequest(Request::capture());
} else {
    // Laravel 10 وما قبلها
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Request::capture()
    )->send();
    $kernel->terminate($request, $response);
}