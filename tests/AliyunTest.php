<?php

declare(strict_types = 1);

namespace Weiran\Sms\Tests;

use JsonException;
use Random\RandomException;
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
     * @throws RandomException
     */
    public function testCaptcha(): void
    {
        $Sms = new Sms();
        if (!$Sms->establish('aliyun:captcha', data_get($this->conf, 'aliyun.with_params'))) {
            $this->fail($Sms->getError()->getMessage());
        }

        $Provider = $this->aliyun();
        if ($Provider->send('captcha', $this->mobile, [
            'code' => random_int(1111, 9999)
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