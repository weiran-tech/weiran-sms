<?php

declare(strict_types = 1);

namespace Weiran\Sms\Tests;

use Illuminate\Support\Str;
use JsonException;
use Weiran\Sms\Action\Sms;
use Weiran\Sms\Classes\ChuanglanSmsProvider;
use Weiran\System\Exceptions\SettingKeyNotMatchException;
use Weiran\System\Exceptions\SettingValueOutOfRangeException;

/**
 * 发送短信
 */
class ChuanglanProviderTest extends BaseSms
{
    /**
     * 测试短信发送
     * @throws JsonException
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException
     */
    public function testCaptcha(): void
    {

        $Sms = new Sms();
        $Sms->establish('chuanglan:no_params', data_get($this->conf, 'chuanglan.no_params'));


        $chuanglan = $this->chuanglan();
        if ($chuanglan->send('no_params', $this->mobile, [
            'code' => 'Test_' . Str::random(4),
        ], (string) data_get($this->conf, 'sign'))) {
            $this->assertTrue(true);
        }
        else {
            $this->fail($chuanglan->getError()->getMessage());
        }
    }

    public function chuanglan(): ChuanglanSmsProvider
    {
        $Sms = new ChuanglanSmsProvider();
        config([
            'weiran.sms.chuanglan.access_key'        => data_get($this->conf, 'chuanglan.access_key'),
            'weiran.sms.chuanglan.access_secret'     => data_get($this->conf, 'chuanglan.access_secret'),
            'weiran.sms.chuanglan.cty_access_key'    => data_get($this->conf, 'chuanglan.cty_access_key'),
            'weiran.sms.chuanglan.cty_access_secret' => data_get($this->conf, 'chuanglan.cty_access_secret'),
        ]);
        return $Sms;
    }
}