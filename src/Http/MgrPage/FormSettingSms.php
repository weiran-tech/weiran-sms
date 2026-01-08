<?php

declare(strict_types = 1);

namespace Weiran\Sms\Http\MgrPage;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Weiran\Core\Classes\Contracts\SettingContract;
use Weiran\Framework\Classes\Resp;
use Weiran\Framework\Validation\Rule;
use Weiran\MgrPage\Classes\Form\Field\Number;
use Weiran\MgrPage\Classes\Form\FormSettingBase;
use Weiran\System\Exceptions\SettingKeyNotMatchException;
use Weiran\System\Exceptions\SettingValueOutOfRangeException;
use Weiran\System\Setting\Repository\SettingRepository;

class FormSettingSms extends FormSettingBase
{
    protected $group = 'weiran-sms::sms';

    protected bool $withContent = true;

    /**
     * @throws SettingValueOutOfRangeException
     * @throws SettingKeyNotMatchException
     * @throws JsonException
     */
    public function handle(Request $request): Response|JsonResponse|RedirectResponse
    {
        $items = $request->all();
        /** @var SettingRepository $Setting */
        $Setting = app(SettingContract::class);
        foreach ($items as $key => $item) {
            if (Str::startsWith($key, 'send_rate_')) {
                $value = (int) $item;
                if ($value < 0 || $value > 100) {
                    return Resp::error('错误的分流比例');
                }
                $Setting->set($this->group . '.' . $key, $value);
            }
        }

        return parent::handle($request);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws SettingValueOutOfRangeException
     * @throws SettingKeyNotMatchException
     * @throws JsonException
     */
    public function form(): void
    {
        $sendTypes = sys_hook('weiran.sms.send_type');
        $table     = [];
        foreach ($sendTypes as $key => $desc) {
            $table[] = [
                (new Number('send_rate_' . $key, [$desc['title']]))->default((int) sys_setting($this->group . '.send_rate_' . $key)),
            ];
        }

        $this->tableInput('rate', '分流比例')->table($table)->help('设置分流比例, 未设置默认为 local');
        $this->text('sign', '默认签名')->rules([Rule::nullable()]);

        foreach ($sendTypes as $desc) {
            if (isset($desc['setting'])) {
                $url  = route($desc['route']);
                $link = <<<Link
<a class="J_iframe" href="$url" data-height="600"><i class="bi bi-sliders"></i> {$desc['title']}设置</a>
Link;
                $this->html($link, $desc['title']);
            }
        }
    }
}
