<?php

declare(strict_types = 1);

namespace Weiran\Sms;

use Weiran\Framework\Exceptions\ModuleNotFoundException;
use Weiran\Framework\Support\WeiranServiceProvider;
use Weiran\Sms\Action\Sms;
use Weiran\Sms\Classes\Contracts\SmsContract;
use Weiran\Sms\Classes\SmsProvider;
use Weiran\Sms\Http\RouteServiceProvider;

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


    private function registerConfig(): void
    {
        // 注册配置
        config([
            'weiran.sms.sign' => sys_setting('wr-sms::sms.sign'),
        ]);
        $sendTypes = array_keys(sys_hook('weiran.sms.send_type'));
        foreach ($sendTypes as $sendType) {
            $rate = (int) sys_setting('wr-sms::sms.send_rate_' . $sendType);
            if ($rate) {
                if ($sendType === Sms::SCOPE_ALIYUN) {
                    config([
                        'weiran.sms.aliyun.access_key'    => sys_setting('wr-sms::sms.aliyun_access_key'),
                        'weiran.sms.aliyun.access_secret' => sys_setting('wr-sms::sms.aliyun_access_secret'),
                    ]);
                }
                if ($sendType === Sms::SCOPE_CHUANGLAN) {
                    config([
                        'weiran.sms.chuanglan.access_key'        => sys_setting('wr-sms::sms.chuanglan_access_key'),
                        'weiran.sms.chuanglan.access_secret'     => sys_setting('wr-sms::sms.chuanglan_access_secret'),
                        'weiran.sms.chuanglan.cty_access_key'    => sys_setting('wr-sms::sms.chuanglan_cty_access_key'),
                        'weiran.sms.chuanglan.cty_access_secret' => sys_setting('wr-sms::sms.chuanglan_cty_access_secret'),
                    ]);
                }
                if ($sendType === Sms::SCOPE_LIANLU) {
                    config([
                        'weiran.sms.lianlu.mch_id'      => sys_setting('wr-sms::sms.lianlu_mch_id'),
                        'weiran.sms.lianlu.app_id'      => sys_setting('wr-sms::sms.lianlu_app_id'),
                        'weiran.sms.lianlu.app_key'     => sys_setting('wr-sms::sms.lianlu_app_key'),
                        'weiran.sms.lianlu.cty_mch_id'  => sys_setting('wr-sms::sms.lianlu_cty_mch_id'),
                        'weiran.sms.lianlu.cty_app_id'  => sys_setting('wr-sms::sms.chuanglan_cty_app_id'),
                        'weiran.sms.lianlu.cty_app_key' => sys_setting('wr-sms::sms.chuanglan_cty_app_key'),
                    ]);
                }
                if ($sendType === Sms::SCOPE_VOLC) {
                    config([
                        'weiran.sms.volc.access_key'      => sys_setting('wr-sms::sms.volc_access_key'),
                        'weiran.sms.volc.access_secret'   => sys_setting('wr-sms::sms.volc_access_secret'),
                        'weiran.sms.volc.default_account' => sys_setting('wr-sms::sms.volc_default_account'),
                    ]);
                }
            }
        }
    }
}
