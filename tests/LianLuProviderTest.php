<?php

declare(strict_types = 1);

namespace Weiran\Sms\Tests;

use JsonException;
use Weiran\Sms\Action\Sms;
use Weiran\Sms\Classes\LianLuSmsProvider;
use Weiran\System\Exceptions\SettingKeyNotMatchException;
use Weiran\System\Exceptions\SettingValueOutOfRangeException;

/**
 * 发送短信
 */
class LianLuProviderTest extends BaseSms
{

    /**
     * 发送普通短信
     * @return void
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException|JsonException
     */
    public function testNoSignSms(): void
    {

        $Sms = new Sms();
        $Sms->establish('lianlu:no_params', data_get($this->conf, 'lianlu.no_params'));

        $lianlu = $this->lianLu();
        if ($lianlu->send('no_params', $this->mobile)) {
            $this->assertTrue(true);
        }
        else {
            $this->fail($lianlu->getError()->getMessage());
        }
    }

    /**
     * 发送带有参数的模板短信
     * @return void
     * @throws JsonException
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException
     */
    public function testWithSignSms(): void
    {
        $Sms = new Sms();
        $Sms->establish('lianlu:with_params', data_get($this->conf, 'lianlu.with_params'));

        $lianlu = $this->lianLu();
        if ($lianlu->send('with_params', $this->mobile, ['验证码'])) {
            $this->assertTrue(true);
        }
        else {
            $this->fail($lianlu->getError()->getMessage());
        }
    }

    private function lianLu(): LianLuSmsProvider
    {
        $Sms = new LianLuSmsProvider();
        config([
            'weiran.sms.sign'           => (string) data_get($this->conf, 'sign'),
            'weiran.sms.lianlu.mch_id'  => data_get($this->conf, 'lianlu.mch_id'),
            'weiran.sms.lianlu.app_id'  => data_get($this->conf, 'lianlu.app_id'),
            'weiran.sms.lianlu.app_key' => data_get($this->conf, 'lianlu.app_key'),
        ]);
        return $Sms;
    }
}