<?php

declare(strict_types = 1);

use Illuminate\Routing\Router;
use Weiran\Sms\Http\Backend\SmsController;
use Weiran\Sms\Http\Backend\StoreController;

Route::group([], function (Router $router) {
    // 短信模版配置
    $router->get('sms', [SmsController::class, 'index'])
        ->name('weiran-sms:backend.sms.index');
    $router->any('sms/establish/{id?}', [SmsController::class, 'establish'])
        ->name('weiran-sms:backend.sms.establish');
    $router->any('sms/destroy/{id}', [SmsController::class, 'destroy'])
        ->name('weiran-sms:backend.sms.destroy');

    $router->any('sms/store', [SmsController::class, 'store'])
        ->name('weiran-sms:backend.sms.store');
    $router->any('store/aliyun', [StoreController::class, 'aliyun'])
        ->name('weiran-sms:backend.store.aliyun');
    $router->any('store/chuanglan', [StoreController::class, 'chuanglan'])
        ->name('weiran-sms:backend.store.chuanglan');
    $router->any('store/lianlu', [StoreController::class, 'lianlu'])
        ->name('weiran-sms:backend.store.lianlu');
    $router->any('store/volc', [StoreController::class, 'volc'])
        ->name('weiran-sms:backend.store.volc');
});
