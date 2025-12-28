<?php

declare(strict_types = 1);

namespace Weiran\Sms\Http\Request\Backend;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Weiran\Framework\Classes\Resp;
use Weiran\MgrPage\Http\Request\Backend\BackendController;
use Weiran\Sms\Action\Sms;
use Weiran\Sms\Http\MgrPage\FormEstablishSms;
use Weiran\Sms\Http\MgrPage\FormSettingSms;
use Weiran\System\Exceptions\SettingKeyNotMatchException;
use Weiran\System\Exceptions\SettingValueOutOfRangeException;

/**
 * 短信控制器
 */
class SmsController extends BackendController
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
    public function index()
    {
        $scope = Sms::SCOPE_LOCAL;
        if (input('_scope')) {
            $scope = input('_scope');
        }
        $templates = $this->action()->getTemplates();
        $items     = $templates->where('scope', $scope);

        return view('weiran-sms::backend.sms.index', [
            'scope' => $scope,
            'items' => $items,
        ]);
    }

    /**
     * 短信模板c2e
     *
     * @return Factory|JsonResponse|RedirectResponse|Response|View
     */
    public function establish()
    {
        return (new FormEstablishSms())->render();
    }

    /**
     * 删除短信模板
     *
     * @param null|string $id id
     *
     * @return JsonResponse|RedirectResponse|Response
     *
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException
     */
    public function destroy(?string $id = null)
    {
        $Sms = $this->action();
        if (!$Sms->destroy($id)) {
            return Resp::error($Sms->getError());
        }

        return Resp::success('操作成功', '_parent_reload|1');
    }

    /**
     * 短信配置
     */
    public function store()
    {
        return (new FormSettingSms())->render();
    }

    private function action(): Sms
    {
        return new Sms();
    }
}
