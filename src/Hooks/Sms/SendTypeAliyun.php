<?php

declare(strict_types = 1);

namespace Weiran\Sms\Hooks\Sms;

use Weiran\Core\Services\Contracts\ServiceArray;
use Weiran\Sms\Classes\AliyunSmsProvider;
use Weiran\Sms\Http\Backend\Store\StoreAliyunForm;

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
            'setting'  => StoreAliyunForm::class,
            'route'    => 'weiran-sms:backend.store.aliyun',
        ];
    }
}
