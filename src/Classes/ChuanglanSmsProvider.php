<?php

declare(strict_types = 1);

namespace Weiran\Sms\Classes;

use Weiran\Framework\Helper\UtilHelper;
use Weiran\Sms\Action\Sms;
use Weiran\Sms\Classes\Chuanglan\SmsApi;
use Weiran\Sms\Classes\Contracts\SmsContract;

class ChuanglanSmsProvider extends BaseSms implements SmsContract
{
    /**
     * @var SmsApi
     */
    private SmsApi $clApi;

    /**
     * @inheritDoc
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
            $output = json_decode($result, true);
            if (isset($output['code']) && $output['code'] === '0') {
                return true;
            }

            return $this->setError($result);
        }

        return $this->setError($result);
    }

    /**
     * 初始化配置
     * @param string $mobiles
     */
    private function initConfig($mobiles): void
    {
        if (!UtilHelper::isChMobile($mobiles)) {
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
