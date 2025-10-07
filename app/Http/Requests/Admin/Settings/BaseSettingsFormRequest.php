<?php

namespace Ruff\Http\Requests\Admin\Settings;

use Illuminate\Validation\Rule;
use Ruff\Traits\Helpers\AvailableLanguages;
use Ruff\Http\Requests\Admin\AdminFormRequest;

class BaseSettingsFormRequest extends AdminFormRequest
{
    use AvailableLanguages;

    public function rules(): array
    {
        return [
            'app:name' => 'required|string|max:191',
            'Ruff:auth:2fa_required' => 'required|integer|in:0,1,2',
            'app:locale' => ['required', 'string', Rule::in(array_keys($this->getAvailableLanguages()))],
        ];
    }

    public function attributes(): array
    {
        return [
            'app:name' => 'Company Name',
            'Ruff:auth:2fa_required' => 'Require 2-Factor Authentication',
            'app:locale' => 'Default Language',
        ];
    }
}
