<?php

declare(strict_types = 1);

namespace Weiran\Sms\Http\MgrPage;

use Weiran\Framework\Validation\Rule;
use Weiran\MgrPage\Classes\Form\FormSettingBase;

class FormSettingChuanglan extends FormSettingBase
{

    protected string $title = '创蓝短信配置';

    protected $withContent = true;

    protected $group = 'py-sms::sms';

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('chuanglan_access_key', '创蓝 Key')->rules([
            Rule::nullable(),
        ]);
        $this->text('chuanglan_access_secret', '创蓝 Secret')->rules([
            Rule::nullable(),
        ]);
        $this->text('chuanglan_cty_access_key', '创蓝国际 Key')->rules([
            Rule::nullable(),
        ]);
        $this->text('chuanglan_cty_access_secret', '创蓝国际 Secret')->rules([
            Rule::nullable(),
        ]);
    }
}
