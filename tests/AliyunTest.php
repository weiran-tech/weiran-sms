<?php

declare(strict_types = 1);

namespace Weiran\Sms\Tests;

use Illuminate\Support\Str;
use JsonException;
use Weiran\Sms\Action\Sms;
use Weiran\Sms\Classes\AliyunSmsProvider;
use Weiran\Sms\Classes\WeiranSmsHelper;
use Weiran\System\Exceptions\SettingKeyNotMatchException;
use Weiran\System\Exceptions\SettingValueOutOfRangeException;

/**
 * 发送短信
 */
class AliyunTest extends BaseSms
{

    public function testTransMobile(): void
    {
        $mobiles = '15555555551';
        // single
        $this->assertEquals('15555555551', WeiranSmsHelper::transToAliyunMobile($mobiles));

        // single
        $mobiles = ['86-15555555552'];
        $this->assertEquals('8615555555552', WeiranSmsHelper::transToAliyunMobile($mobiles));

        // multi
        $mobiles = ['15555555553', '16666666666'];
        $this->assertEquals('15555555553,16666666666', WeiranSmsHelper::transToAliyunMobile($mobiles));

        // multi string
        $mobiles = '15555555554,16666666666';
        $this->assertEquals('15555555554,16666666666', WeiranSmsHelper::transToAliyunMobile($mobiles));
    }

    /**
     * 测试短信发送
     * @return void
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException
     * @throws JsonException
     */
    public function testCaptcha(): void
    {
        $Sms = new Sms();
        $Sms->establish('aliyun:no_params', data_get($this->conf, 'aliyun.no_params'));

        $Provider = new AliyunSmsProvider();
        if ($Provider->send('no_params', $this->mobile, [
            'code' => 'Test_' . Str::random(4),
        ])) {
            $this->assertTrue(true);
        }
        else {
            $this->fail($Provider->getError()->getMessage());
        }
    }

    public function aliyun(): AliyunSmsProvider
    {
        $Sms = new AliyunSmsProvider();
        // config
        config([
            'weiran.sms.aliyun.access_key'    => data_get($this->conf, 'aliyun.access_key'),
            'weiran.sms.aliyun.access_secret' => data_get($this->conf, 'aliyun.access_secret'),
        ]);
        return $Sms;
    }
}