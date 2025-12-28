<?php

declare(strict_types = 1);

namespace Weiran\Sms\Hooks\Sms;

use Weiran\Core\Services\Contracts\ServiceArray;
use Weiran\Sms\Classes\LianLuSmsProvider;
use Weiran\Sms\Http\MgrPage\FormSettingLianLu;

class SendTypeLianLu implements ServiceArray
{
    public function key(): string
    {
        return 'lianlu';
    }

    public function data(): array
    {
        return [
            'title'    => '联麓',
            'provider' => LianLuSmsProvider::class,
            'setting'  => FormSettingLianLu::class,
            'route'    => 'weiran-sms:backend.store.lianlu',
        ];
    }
}
