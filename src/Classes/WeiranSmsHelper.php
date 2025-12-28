<?php

declare(strict_types = 1);

namespace Weiran\Sms\Classes;

/**
 * Sms Helper
 */
class WeiranSmsHelper
{
    /**
     * 模板缓存
     */
    /**
     * @param array|string $mobile
     */
    public static function transToAliyunMobile(mixed $mobile): string
    {
        return array_reduce((array) $mobile, function ($carry, $mobile) {
            $mobile = str_replace('-', '', $mobile);

            return $carry ? $carry . ',' . $mobile : $mobile;
        }, '');
    }
}
