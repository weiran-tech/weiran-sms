<?php

declare(strict_types = 1);

namespace Weiran\Sms\Http\MgrPage;

use Weiran\Framework\Validation\Rule;
use Weiran\MgrPage\Classes\Form\FormSettingBase;

class FormSettingVolc extends FormSettingBase
{
    protected string $title = '火山云短信配置';

    protected bool $withContent = true;

    protected $group = 'weiran-sms::sms';

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('volc_access_key', 'AccessKey')->rules([
            Rule::nullable(),
        ]);
        $this->text('volc_access_secret', 'AccessSecret')->rules([
            Rule::nullable(),
        ]);
        $this->text('volc_default_account', '默认消息组ID')->rules([
            Rule::nullable(),
        ]);
    }
}
