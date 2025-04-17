<?php

declare(strict_types = 1);

namespace Weiran\Sms;

use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Weiran\Framework\Exceptions\ModuleNotFoundException;
use Weiran\Framework\Support\WeiranServiceProvider;
use Weiran\Sms\Action\Sms;
use Weiran\Sms\Classes\Contracts\SmsContract;
use Weiran\Sms\Classes\SmsProvider;
use Weiran\Sms\Http\RouteServiceProvider;
use Weiran\System\Exceptions\SettingKeyNotMatchException;
use Weiran\System\Exceptions\SettingValueOutOfRangeException;

class ServiceProvider extends WeiranServiceProvider
{

    /**
     * Bootstrap the application events.
     * @return void
     * @throws ModuleNotFoundException
     */
    public function boot(): void
    {
        parent::boot('weiran.sms');
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        $this->registerConfig();

        // 配置文件
        $this->mergeConfigFrom(dirname(__DIR__) . '/resources/config/sms.php', 'weiran.sms');

        $this->app->bind('weiran.sms', function () {
            return new SmsProvider;
        });

        $this->app->alias('weiran.sms', SmsContract::class);
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides(): array
    {
        return [
            'weiran.sms',
        ];
    }


    /**
     * @return void
     * @throws JsonException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException
     */
    private function registerConfig(): void
    {
        // 注册配置
        config([
            'weiran.sms.sign' => sys_setting('weiran-sms::sms.sign'),
        ]);
    }
}
