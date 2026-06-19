<?php

namespace Ruff\Http\Requests\Admin\Settings;

use Ruff\Http\Requests\Admin\AdminFormRequest;

class ThemeSettingsFormRequest extends AdminFormRequest
{
    public function rules(): array
    {
        // Accept hex, rgb()/rgba(), hsl()/hsla() and named colors — but nothing
        // that could break out of the CSS value context when emitted.
        $color = ['nullable', 'string', 'max:64', 'regex:/^(#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})|rgba?\([0-9.,%\s\/]+\)|hsla?\([0-9.,%\s\/a-z]+\)|[a-zA-Z]+)$/'];

        return [
            'default_mode' => 'required|in:light,dark',
            'allow_toggle' => 'sometimes|nullable',
            'share_accent' => 'sometimes|nullable',
            'geometry' => 'required|array',
            'geometry.*' => ['nullable', 'string', 'max:32', 'regex:/^[0-9a-zA-Z.%\s()]+$/'],
            'light' => 'required|array',
            'light.*' => $color,
            'dark' => 'required|array',
            'dark.*' => $color,
        ];
    }

    public function attributes(): array
    {
        return [
            'default_mode' => 'Default mode',
            'geometry' => 'Geometry',
            'light' => 'Light mode colors',
            'dark' => 'Dark mode colors',
        ];
    }
}
