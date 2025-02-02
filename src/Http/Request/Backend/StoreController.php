<?php

declare(strict_types = 1);

namespace Weiran\Sms\Http\Request\Backend;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Weiran\MgrPage\Http\Request\Backend\BackendController;
use Weiran\Sms\Http\MgrPage\FormSettingAliyun;
use Weiran\Sms\Http\MgrPage\FormSettingChuanglan;
use Weiran\Sms\Http\MgrPage\FormSettingLianLu;
use Weiran\Sms\Http\MgrPage\FormSettingVolc;

/**
 * 短信控制器
 */
class StoreController extends BackendController
{

    public function __construct()
    {
        parent::__construct();

        self::$permission = [
            'global' => 'backend:py-sms.global.manage',
        ];
    }

    /**
     * @return Factory|View
     */
    public function aliyun()
    {
        return (new FormSettingAliyun())->render();
    }

    /**
     * @return Factory|View
     */
    public function chuanglan()
    {
        return (new FormSettingChuanglan())->render();
    }

    /**
     * @return Factory|View
     */
    public function lianlu()
    {
        return (new FormSettingLianLu())->render();
    }

    /**
     * @return Factory|View
     */
    public function volc()
    {
        return (new FormSettingVolc())->render();
    }
}