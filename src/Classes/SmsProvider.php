<?php
declare(strict_types = 1);

namespace Weiran\Sms\Classes;

use Weiran\Sms\Action\Sms;
use Weiran\Sms\Classes\Contracts\SmsContract;

class SmsProvider extends BaseSms implements SmsContract
{
    protected $driver = '';

    public function setDriver($driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * 通过短信类型获取 Driver
     */
    public function getDriver(string $type): SmsContract
    {
        if ($this->driver) {
            return new $this->driver;
        }

        $driver = Sms::rateSmsDriverByType($type);
        if (!$driver) {
            $driver = Sms::SCOPE_LOCAL;
        }

        $hooks       = sys_hook('poppy.sms.send_type');
        $sender      = $hooks[$driver];
        $senderClass = $sender['provider'] ?? LocalSmsProvider::class;

        /** @var SmsContract $Sms */
        return new $senderClass();
    }

    /**
     * 发送短信
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