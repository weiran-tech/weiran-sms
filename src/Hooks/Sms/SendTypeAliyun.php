<?php

declare(strict_types = 1);

namespace Weiran\Sms\Hooks\Sms;

use Weiran\Core\Services\Contracts\ServiceArray;
use Weiran\Sms\Classes\AliyunSmsProvider;
use Weiran\Sms\Http\MgrPage\FormSettingAliyun;

class SendTypeAliyun implements ServiceArray
{
    public function key(): string
    {
        return 'aliyun';
    }

    public function data(): array
    {
        return [
            'title'    => '阿里云',
            'provider' => AliyunSmsProvider::class,
            'setting'  => FormSettingAliyun::class,
            'route'    => 'weiran-sms:backend.store.aliyun',
        ];
    }
}
