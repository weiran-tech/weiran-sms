<?php

declare(strict_types = 1);

namespace Weiran\Sms\Action;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;
use Weiran\Framework\Classes\Traits\AppTrait;
use Weiran\Framework\Exceptions\HintException;
use Weiran\Sms\Classes\WeiranSmsDef;
use Weiran\System\Classes\Traits\SystemTrait;
use Weiran\System\Exceptions\SettingKeyNotMatchException;
use Weiran\System\Exceptions\SettingValueOutOfRangeException;

/**
 * 短信模板
 */
class Sms
{
    use AppTrait, SystemTrait;
    public const SCOPE_LOCAL     = 'local';
    public const SCOPE_ALIYUN    = 'aliyun';
    public const SCOPE_CHUANGLAN = 'chuanglan';
    public const SCOPE_LIANLU    = 'lianlu';
    public const SCOPE_VOLC      = 'volc';

    /**
     * 所有的模版
     */
    private Collection $templates;

    /**
     * 项目条目
     */
    private array $item;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws SettingValueOutOfRangeException
     * @throws SettingKeyNotMatchException
     * @throws JsonException
     */
    public function __construct()
    {
        $this->templates = collect(sys_setting(WeiranSmsDef::ckTemplate(), []) ?: []);
    }

    /**
     * 短信类型
     *
     * @param string|null $key key
     */
    public static function kvType(?string $key = null): array|string
    {
        $desc = collect(config('weiran.sms.types') ?: [])->pluck('title', 'type')->toArray();

        return kv($desc, $key);
    }

    /**
     * 平台类型
     */
    public static function kvPlatform(?string $key = null): array|string
    {
        $sendTypes     = sys_hook('weiran.sms.send_type');
        $desc['local'] = '本地';
        foreach ($sendTypes as $k => $d) {
            $desc[$k] = $d['title'];
        }

        return kv($desc, $key);
    }

    /**
     * 获取指定平台对应类型的模板
     *
     * @param string $type 类型
     *
     * @return array [type|code|content]
     */
    public static function smsTpl(string $type, string $scope): array
    {
        $templates = collect((new Sms())->getTemplates());
        $key       = $scope . ':' . $type;
        if ($templates->offsetExists($key)) {
            return $templates->offsetGet($key);
        }

        return [];
    }

    /**
     * 获取现在配置分流的短信
     *
     * @throws ContainerExceptionInterface
     * @throws JsonException
     * @throws NotFoundExceptionInterface
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException
     */
    public static function rateSmsType(): string
    {
        $sendTypes = array_keys(sys_hook('weiran.sms.send_type'));

        return self::rateSmsDriver($sendTypes);
    }

    /**
     * 获取所有的驱动
     */
    public static function getSendType()
    {
        return sys_hook('weiran.sms.send_type');
    }

    /**
     * 实现原理：
     * 1. 获取所有的平台
     * 2. 过滤存在该模板的平台
     * 3. 再次进行过滤
     *
     * @throws ContainerExceptionInterface
     * @throws JsonException
     * @throws NotFoundExceptionInterface
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException
     */
    public static function rateSmsDriverByType(string $type): string
    {
        // 获取所有的模板
        $templates = collect((new Sms())->getTemplates());

        // 获取所有的驱动
        $hooks = sys_hook('weiran.sms.send_type');

        // 对驱动过滤，查找符合条件的驱动
        $types = [];
        foreach (array_keys($hooks) as $hook) {
            if ($templates[$hook . ':' . $type] ?? null) {
                $types[] = $hook;
            }
        }

        return self::rateSmsDriver($types);
    }

    /**
     * 抽取公共方法
     *
     * @throws ContainerExceptionInterface
     * @throws JsonException
     * @throws NotFoundExceptionInterface
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException
     */
    private static function rateSmsDriver(array $sendTypes): string
    {
        $rates = [];
        foreach ($sendTypes as $sendType) {
            $rate = (int) sys_setting('weiran-sms::sms.send_rate_' . $sendType);
            if ($rate) {
                $rates[$sendType] = $rate;
            }
        }

        return self::getRandType($rates);
    }

    private static function getRandType(array $rates): string
    {
        try {
            $result = self::SCOPE_LOCAL;
            if (!$rates) {
                return $result;
            }
            if (count($rates) === 1) {
                return (string) array_key_first($rates);
            }
            // 概率数组的总概率精度
            $sumRates = array_sum($rates);
            // 概率数组循环
            foreach ($rates as $key => $rate) {
                $randNum = random_int(1, $sumRates);
                if ($randNum <= $rate) {
                    $result = $key;
                    break;
                }
                $sumRates -= $rate;
            }

            return (string) $result;
        }
        catch (Throwable) {
            return $result;
        }
    }

    /**
     * 获取所有的模版
     */
    public function getTemplates(): Collection
    {
        return $this->templates;
    }

    public function getItem(): array
    {
        return $this->item;
    }

    /**
     * 新增和编辑
     *
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException|JsonException
     */
    public function establish(string $id, string $code): bool
    {
        [$scope, $type] = explode(':', $id);
        $this->templates->offsetSet($id, [
            'scope' => $scope,
            'type'  => $type,
            'code'  => $code,
        ]);

        return $this->save();
    }

    /**
     * 初始化
     *
     * @param string $id ID
     *
     * @throws HintException
     */
    public function init(string $id): bool
    {
        if (!Str::contains($id, ':')) {
            throw new HintException('ID 类型错误, ID 格式应当为 type:id');
        }
        $items = collect($this->templates);
        if ($items->offsetExists($id)) {
            $this->item = $items->offsetGet($id);

            return true;
        }
        throw new HintException('短信ID不存在');
    }

    /**
     * 刪除
     *
     * @param string $id id
     *
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException|JsonException
     */
    public function destroy(string $id): bool
    {
        if (isset($this->templates[$id])) {
            unset($this->templates[$id]);
        }

        return $this->save();
    }

    /**
     * 保存模板
     *
     * @throws SettingKeyNotMatchException
     * @throws SettingValueOutOfRangeException|JsonException
     */
    private function save(): bool
    {
        $this->sysSetting()->set(WeiranSmsDef::ckTemplate(), $this->templates->toArray());

        return true;
    }
}
