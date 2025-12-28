<?php

declare(strict_types = 1);

namespace Weiran\Sms\Classes;

use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Volc\Service\Sms as VolcSms;
use Weiran\Sms\Action\Sms;
use Weiran\Sms\Classes\Contracts\SmsContract;
use Weiran\Sms\Exceptions\SmsException;
use Weiran\System\Classes\Logger\Logging;
use Weiran\System\Exceptions\SettingKeyNotMatchException;
use Weiran\System\Exceptions\SettingValueOutOfRangeException;

class VolcSmsProvider extends BaseSms implements SmsContract
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws SettingKeyNotMatchException
     * @throws JsonException
     * @throws SettingValueOutOfRangeException
     */
    public function __construct()
    {
        parent::__construct();
        config([
            'weiran.sms.volc.access_key'      => sys_setting('weiran-sms::sms.volc_access_key'),
            'weiran.sms.volc.access_secret'   => sys_setting('weiran-sms::sms.volc_access_secret'),
            'weiran.sms.volc.default_account' => sys_setting('weiran-sms::sms.volc_default_account'),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function send(string $type, $mobile, array $params = [], $sign = ''): bool
    {
        $this->setScope(Sms::SCOPE_VOLC);
        if (!$this->checkSms($mobile, $type, $sign)) {
            return false;
        }

        $templateId = $this->sms['code'] ?? '';

        $smsAccount = sys_get($params, 'SmsAccount', config('weiran.sms.volc.default_account'));
        unset($params['SmsAccount']);

        $templateParam = $params;

        // 支持数组/字串/多字串
        $mobile = array_reduce((array) $mobile, function ($carry, $mobile) {
            $mobile = str_replace('-', '', $mobile);

            return $carry ? $carry . ',' . $mobile : $mobile;
        }, '');

        try {
            $client = $this->initClient();

            /**
             * @url https://www.volcengine.com/docs/6361/1109262
             */
            $body = [
                'SmsAccount'    => $smsAccount,
                'Sign'          => $this->sign,
                'TemplateID'    => $templateId,
                'TemplateParam' => json_encode($templateParam),
                'PhoneNumbers'  => $mobile,
            ];

            $this->logger()->info('volc.request', [
                'body' => $body,
            ]);

            $response = $client->sendSms([
                'json' => $body,
            ]);

            /**
             * 返回成功示例
             * {
             *  "ResponseMetadata": {
             *      "RequestId": "202211221049040101310571****",
             *      "Action": "SendSms",
             *      "Version": "2020-01-01",
             *      "Service": "volcSMS",
             *      "Region": "cn-north-1"
             *  },
             *  "Result": {
             *      "MessageID": [
             *          "31293de5-9ef6-4e11-abcd-69659****"
             *      ]
             *  }
             * }
             */

            /**
             * 返回失败示例
             * {
             *  "ResponseMetadata": {
             *      "RequestId": "202211221050270101330310****",
             *      "Action": "SendSms",
             *      "Version": "2020-01-01",
             *      "Service": "volcSMS",
             *      "Region": "cn-north-1",
             *      "Error": {
             *          "Code": "RE:0003",
             *          "Message": "SmsAccount not exist."
             *      }
             *  }，
             *  "Result": {
             *      "MessageID": [
             *          "31283de5-9ef6-5e11-abcd-69659****"
             *      ]
             *  }
             * }
             */
            $result = json_decode((string) $response->getContents(), true);
            $this->logger()->info('volc.response', [
                'result' => $result,
            ]);

            $error = data_get($result, 'ResponseMetadata.Error');
            if (empty($error)) {
                return true;
            }

            $errorMessage = data_get((array) $error, 'Message');

            return $this->setError('Volc:' . $errorMessage);
        }
        catch (SmsException $e) {
            return $this->setError($e->getMessage());
        }
    }

    protected function logger()
    {
        return Logging::logger('Sms');
    }

    /**
     * 初始化
     *
     * @throws SmsException
     */
    private function initClient()
    {
        $accessKeyId     = config('weiran.sms.volc.access_key');
        $accessKeySecret = config('weiran.sms.volc.access_secret');

        $client = VolcSms::getInstance();
        $client->setAccessKey($accessKeyId);
        $client->setSecretKey($accessKeySecret);

        if (!class_exists(VolcSms::class)) {
            throw new SmsException('你需要手动安装 `volcengine/volc-sdk-php` 组件');
        }

        return $client;
    }
}
