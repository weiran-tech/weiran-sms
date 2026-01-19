<?php

declare(strict_types = 1);

namespace Weiran\Sms\Http\Backend;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Weiran\MgrPage\Http\Backend\BackendController;
use Weiran\Sms\Http\Backend\Store\StoreAliyunForm;
use Weiran\Sms\Http\Backend\Store\StoreChuanglanForm;
use Weiran\Sms\Http\Backend\Store\StoreLianLuForm;
use Weiran\Sms\Http\Backend\Store\StoreVolcForm;

/**
 * 短信控制器
 */
class StoreController extends BackendController
{
    public function __construct()
    {
        parent::__construct();

        self::$permission = [
            'global' => 'backend:weiran-sms.global.manage',
        ];
    }

    /**
     * @return Factory|View
     */
    public function aliyun()
    {
        return (new StoreAliyunForm())->render();
    }

    /**
     * @return Factory|View
     */
    public function chuanglan()
    {
        return (new StoreChuanglanForm())->render();
    }

    /**
     * @return Factory|View
     */
    public function lianlu()
    {
        return (new StoreLianLuForm())->render();
    }

    /**
     * @return Factory|View
     */
    public function volc()
    {
        return (new StoreVolcForm())->render();
    }
}
