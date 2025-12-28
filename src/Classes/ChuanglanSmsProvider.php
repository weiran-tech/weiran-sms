<?php

declare(strict_types = 1);

namespace Weiran\Sms\Classes;

use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Weiran\Framework\Helper\UtilHelper;
use Weiran\Sms\Action\Sms;
use Weiran\Sms\Classes\Chuanglan\SmsApi;
use Weiran\Sms\Classes\Contracts\SmsContract;
use Weiran\System\Exceptions\SettingKeyNotMatchException;
use Weiran\System\Exceptions\SettingValueOutOfRangeException;

class ChuanglanSmsProvider extends BaseSms implements SmsContract
{
    private SmsApi $clApi;

    /**
     * @throws ContainerExceptionInterface
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException
     * @throws NotFoundExceptionInterface
     * @throws JsonException
     */
    public function __construct()
    {
        parent::__construct();
        config([
            'weiran.sms.chuanglan.access_key'        => sys_setting('weiran-sms::sms.chuanglan_access_key'),
            'weiran.sms.chuanglan.access_secret'     => sys_setting('weiran-sms::sms.chuanglan_access_secret'),
            'weiran.sms.chuanglan.cty_access_key'    => sys_setting('weiran-sms::sms.chuanglan_cty_access_key'),
            'weiran.sms.chuanglan.cty_access_secret' => sys_setting('weiran-sms::sms.chuanglan_cty_access_secret'),
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @throws JsonException
     */
    public function send(string $type, $mobile, array $params = [], $sign = ''): bool
    {
        $this->setScope(Sms::SCOPE_CHUANGLAN);
        if (!$this->checkSms($mobile, $type, $sign)) {
            return false;
        }
        $this->initConfig($mobile);

        $msg = sys_trans($this->sms['code'], $params);
        // 拼接签名
        $msg = '【' . str_replace(['【', '】', '[', ']'], '', $this->sign) . '】' . $msg;

        $result = UtilHelper::isChMobile($mobile)
            ? $this->clApi->sendSms($mobile, $msg)
            : $this->clApi->sendCtySMS($mobile, $msg);
        if (!is_null($result)) {
            $output = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
            if (isset($output['code']) && $output['code'] === '0') {
                return true;
            }

            return $this->setError($result);
        }

        return $this->setError($result);
    }

    /**
     * 初始化配置
     */
    private function initConfig(string $mobile): void
    {
        if (!UtilHelper::isChMobile($mobile)) {
            $apiAccount  = config('weiran.sms.chuanglan.cty_access_key');
            $apiPassword = config('weiran.sms.chuanglan.cty_access_secret');
        }
        else {
            $apiAccount  = config('weiran.sms.chuanglan.access_key');
            $apiPassword = config('weiran.sms.chuanglan.access_secret');
        }
        $this->clApi = new SmsApi($apiAccount, $apiPassword);
    }
}
