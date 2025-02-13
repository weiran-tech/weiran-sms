<?php

declare(strict_types = 1);

namespace Weiran\Sms\Classes;

use Weiran\Framework\Helper\UtilHelper;
use Weiran\Sms\Action\Sms;
use Weiran\Sms\Classes\Contracts\SmsContract;
use Weiran\Sms\Classes\LianLu\SmsApi;

class LianLuSmsProvider extends BaseSms implements SmsContract
{
    /**
     * @var SmsApi
     */
    private SmsApi $llApi;

    /**
     * @inheritDoc
     */
    public function send(string $type, $mobile, array $params = [], $sign = ''): bool
    {
        $this->setScope(Sms::SCOPE_LIANLU);
        if (!$this->checkSms($mobile, $type, $sign)) {
            return false;
        }

        $this->initConfig($mobile);

        $templateId     = $this->sms['code'] ?? '';
        $templateParams = array_values($params);
        if (!$templateId) {
            return $this->setError('未设置模版ID');
        }
        if (!UtilHelper::isChMobile($mobile)) {
            return $this->setError('暂不支持国际短信发送');
        }
        $result = $this->llApi->sendTemplateSMS($mobile, $templateId, $templateParams);
        if (!is_null($result)) {
            $output = json_decode($result, true);
            if (isset($output['status']) && $output['status'] === '00') {
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
            $mchId  = config('weiran.sms.lianlu.cty_mch_id');
            $appId  = config('weiran.sms.lianlu.cty_app_id');
            $appKey = config('weiran.sms.lianlu.cty_app_key');
        }
        else {
            $mchId  = config('weiran.sms.lianlu.mch_id');
            $appId  = config('weiran.sms.lianlu.app_id');
            $appKey = config('weiran.sms.lianlu.app_key');
        }
        $this->llApi = new SmsApi($mchId, $appId, $appKey, $this->sign);
    }
}
