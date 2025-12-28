<?php

declare(strict_types = 1);

namespace Weiran\Sms\Classes;

/**
 * 短信定义
 */
class WeiranSmsDef
{
    /**
     * 模板缓存
     */
    public static function ckTemplate(): string
    {
        return 'weiran-sms::sms.template';
    }
}
