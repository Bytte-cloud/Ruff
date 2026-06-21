<?php

namespace Ruff\Http\Requests\Admin;

use Ruff\Models\Announcement;

class AnnouncementFormRequest extends AdminFormRequest
{
    public function rules(): array
    {
        if ($this->method() === 'PATCH') {
            return Announcement::getRulesForUpdate($this->route()->parameter('announcement'));
        }

        return Announcement::getRules();
    }

    /**
     * Normalise the checkbox + optional inputs before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'enabled' => $this->boolean('enabled'),
            'link' => $this->input('link') ?: null,
            'sort_order' => (int) $this->input('sort_order', 0),
        ]);
    }
}
