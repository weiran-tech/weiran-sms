<?php

declare(strict_types = 1);

namespace Weiran\Sms\Http\Validation;

use Weiran\Framework\Application\Request;
use Weiran\Framework\Validation\Rule;
use Weiran\Sms\Action\Sms;

class SmsEstablishRequest extends Request
{

    public function rules(): array
    {
        return [
            'type'  => [
                Rule::required(),
                Rule::in(array_keys(Sms::kvType())),
            ],
            'scope' => [
                Rule::required(),
            ],
            'code'  => [
                Rule::required()
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'scope' => '平台类型',
            'type'  => '短信类型',
            'code'  => '短信模版',
        ];
    }
}