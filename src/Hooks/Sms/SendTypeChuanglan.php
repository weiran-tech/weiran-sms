<?php

declare(strict_types = 1);

namespace Weiran\Sms\Hooks\Sms;

use Weiran\Core\Services\Contracts\ServiceArray;
use Weiran\Sms\Classes\ChuanglanSmsProvider;
use Weiran\Sms\Http\MgrPage\FormSettingChuanglan;

class SendTypeChuanglan implements ServiceArray
{

    public function key(): string
    {
        return 'chuanglan';
    }

    public function data():array
    {
        return [
            'title'    => '创蓝',
            'provider' => ChuanglanSmsProvider::class,
            'setting'  => FormSettingChuanglan::class,
            'route'    => 'py-sms:backend.store.chuanglan',
        ];
    }
}