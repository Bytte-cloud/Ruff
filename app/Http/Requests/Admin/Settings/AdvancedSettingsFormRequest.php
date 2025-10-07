<?php

namespace Ruff\Http\Requests\Admin\Settings;

use Ruff\Http\Requests\Admin\AdminFormRequest;

class AdvancedSettingsFormRequest extends AdminFormRequest
{
    /**
     * Return all the rules to apply to this request's data.
     */
    public function rules(): array
    {
        return [
            'recaptcha:enabled' => 'required|in:true,false',
            'recaptcha:secret_key' => 'required|string|max:191',
            'recaptcha:website_key' => 'required|string|max:191',
            'Ruff:guzzle:timeout' => 'required|integer|between:1,60',
            'Ruff:guzzle:connect_timeout' => 'required|integer|between:1,60',
            'Ruff:client_features:allocations:enabled' => 'required|in:true,false',
            'Ruff:client_features:allocations:range_start' => [
                'nullable',
                'required_if:Ruff:client_features:allocations:enabled,true',
                'integer',
                'between:1024,65535',
            ],
            'Ruff:client_features:allocations:range_end' => [
                'nullable',
                'required_if:Ruff:client_features:allocations:enabled,true',
                'integer',
                'between:1024,65535',
                'gt:Ruff:client_features:allocations:range_start',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'recaptcha:enabled' => 'reCAPTCHA Enabled',
            'recaptcha:secret_key' => 'reCAPTCHA Secret Key',
            'recaptcha:website_key' => 'reCAPTCHA Website Key',
            'Ruff:guzzle:timeout' => 'HTTP Request Timeout',
            'Ruff:guzzle:connect_timeout' => 'HTTP Connection Timeout',
            'Ruff:client_features:allocations:enabled' => 'Auto Create Allocations Enabled',
            'Ruff:client_features:allocations:range_start' => 'Starting Port',
            'Ruff:client_features:allocations:range_end' => 'Ending Port',
        ];
    }
}
