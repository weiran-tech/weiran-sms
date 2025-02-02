<?php

declare(strict_types = 1);

namespace Weiran\Sms\Hooks\Sms;

use Weiran\Core\Services\Contracts\ServiceArray;
use Weiran\Sms\Classes\LocalSmsProvider;

class SendTypeLocal implements ServiceArray
{

    public function key(): string
    {
        return 'local';
    }

    public function data():array
    {
        return [
            'title'    => '本地',
            'provider' => LocalSmsProvider::class,
        ];
    }
}