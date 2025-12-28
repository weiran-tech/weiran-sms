<?php

declare(strict_types = 1);

namespace Weiran\Sms\Classes;

use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Weiran\Sms\Action\Sms;
use Weiran\Sms\Classes\Contracts\SmsContract;
use Weiran\System\Exceptions\SettingKeyNotMatchException;
use Weiran\System\Exceptions\SettingValueOutOfRangeException;

class SmsProvider extends BaseSms implements SmsContract
{
    protected string $driver = '';

    public function __construct()
    {
        parent::__construct();
        config([
            'weiran.sms.sign' => sys_setting('weiran-sms::sms.sign'),
        ]);
    }

    public function setDriver($driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * 通过短信类型获取 Driver
     *
     * @throws JsonException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException
     */
    public function getDriver(string $type): SmsContract
    {
        $driver = Sms::rateSmsDriverByType($type);
        if (!$driver) {
            $driver = Sms::SCOPE_LOCAL;
        }

        $hooks       = sys_hook('weiran.sms.send_type');
        $sender      = $hooks[$driver];
        $senderClass = $sender['provider'] ?? LocalSmsProvider::class;

        /** @var SmsContract $Sms */
        return new $senderClass();
    }

    /**
     * 发送短信
     *
     * @throws ContainerExceptionInterface
     * @throws JsonException
     * @throws NotFoundExceptionInterface
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException
     */
    public function send(string $type, $mobile, array $params = [], string $sign = ''): bool
    {
        $driver = $this->getDriver($type);

        if ($driver->send($type, $mobile, $params, $sign)) {
            return true;
        }

        return $this->setError($driver->getError());
    }
}
