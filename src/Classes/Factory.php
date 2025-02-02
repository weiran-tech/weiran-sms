<?php

declare(strict_types = 1);

namespace Weiran\Sms\Classes;

use Weiran\Framework\Classes\Traits\AppTrait;
use Weiran\Sms\Action\Sms;
use Weiran\Sms\Classes\Contracts\SmsContract;
use SimpleXMLElement;

class Factory
{

    /**
     * @var SmsContract|null
     */
    private static ?SmsContract $instance = null;

    /**
     * @return mixed|SimpleXMLElement
     */
    public static function instance(): BaseSms
    {
        $sendType = Sms::rateSmsType();
        $hooks    = sys_hook('poppy.sms.send_type');
        if (!$sendType) {
            $sendType = Sms::SCOPE_LOCAL;
        }
        $sender      = $hooks[$sendType];
        $senderClass = $sender['provider'] ?? LocalSmsProvider::class;
        /** @var SmsContract|AppTrait $Sms */
        return new $senderClass();
    }
}
