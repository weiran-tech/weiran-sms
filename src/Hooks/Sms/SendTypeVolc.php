<?php

declare(strict_types = 1);

namespace Weiran\Sms\Hooks\Sms;

use Weiran\Core\Services\Contracts\ServiceArray;
use Weiran\Sms\Classes\VolcSmsProvider;
use Weiran\Sms\Http\MgrPage\FormSettingVolc;

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
            'setting'  => FormSettingVolc::class,
            'route'    => 'py-sms:backend.store.volc',
        ];
    }
}