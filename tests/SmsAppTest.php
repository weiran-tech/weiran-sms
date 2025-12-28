<?php

declare(strict_types = 1);

namespace Weiran\Sms\Tests;

use Illuminate\Support\Str;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Weiran\Sms\Classes\Contracts\SmsContract;
use Weiran\System\Exceptions\SettingKeyNotMatchException;
use Weiran\System\Exceptions\SettingValueOutOfRangeException;

/**
 * 测试封装的内容可以调用
 * 发送短信
 */
class SmsAppTest extends BaseSms
{
    /**
     * 测试短信发送
     *
     * @throws JsonException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException
     */
    public function testCaptcha(): void
    {
        $Sms = app('weiran.sms');
        if ($Sms->send('captcha', $this->mobile, [
            'code' => 'Test_' . Str::random(4),
        ])) {
            $this->assertTrue(true);
        }
        else {
            $this->fail($Sms->getError()->getMessage());
        }
    }

    public function testContract(): void
    {
        $Sms = app(SmsContract::class);
        if ($Sms->send('captcha', $this->mobile, [
            'code' => 'Test_' . Str::random(4),
        ])) {
            $this->assertTrue(true);
        }
        else {
            $this->fail($Sms->getError()->getMessage());
        }
    }
}
