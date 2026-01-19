<?php

declare(strict_types = 1);

namespace Weiran\Sms\Hooks\Sms;

use Weiran\Core\Services\Contracts\ServiceArray;
use Weiran\Sms\Classes\VolcSmsProvider;
use Weiran\Sms\Http\Backend\Store\StoreVolcForm;

/**
 * 火山云
 */
class SendTypeVolc implements ServiceArray
{
    public function key(): string
    {
        return 'volc';
    }

    public function data()
    {
        return [
            'title'    => '火山云',
            'provider' => VolcSmsProvider::class,
            'setting'  => StoreVolcForm::class,
            'route'    => 'weiran-sms:backend.store.volc',
        ];
    }
}
