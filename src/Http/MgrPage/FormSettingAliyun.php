<?php

declare(strict_types = 1);

namespace Weiran\Sms\Http\MgrPage;

use Weiran\Framework\Validation\Rule;
use Weiran\MgrPage\Classes\Form\FormSettingBase;

class FormSettingAliyun extends FormSettingBase
{
    protected string $title = '阿里云短信配置';

    protected bool $withContent = true;

    protected string $group = 'weiran-sms::sms';

    /**
     * Build a form here.
     */
    public function form(): void
    {
        $this->text('aliyun_access_key', '阿里云 Key')->rules([
            Rule::nullable(),
        ]);
        $this->text('aliyun_access_secret', '阿里云 Secret')->rules([
            Rule::nullable(),
        ]);
    }
}
