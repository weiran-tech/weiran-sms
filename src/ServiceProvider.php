<?php

declare(strict_types = 1);

namespace Weiran\Sms;

use Weiran\Framework\Exceptions\ModuleNotFoundException;
use Weiran\Framework\Support\WeiranServiceProvider;
use Weiran\Sms\Classes\Contracts\SmsContract;
use Weiran\Sms\Classes\SmsProvider;
use Weiran\Sms\Http\RouteServiceProvider;

class ServiceProvider extends WeiranServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @throws ModuleNotFoundException
     */
    public function boot(): void
    {
        parent::boot('weiran.sms');
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        // 配置文件
        $this->mergeConfigFrom(dirname(__DIR__) . '/resources/config/sms.php', 'weiran.sms');

        $this->app->bind('weiran.sms', function () {
            return new SmsProvider();
        });

        $this->app->alias('weiran.sms', SmsContract::class);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'weiran.sms',
        ];
    }
}
