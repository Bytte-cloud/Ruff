<?php

namespace Ruff\Http\Requests\Admin;

use Ruff\Models\Pup;

class PupFormRequest extends AdminFormRequest
{
    /**
     * Set up the validation rules to use for these requests.
     */
    public function rules(): array
    {
        if ($this->method() === 'PATCH') {
            return Pup::getRulesForUpdate($this->route()->parameter('pup'));
        }

        return Pup::getRules();
    }

    /**
     * Treat a blank firmware selection as "let the daemon infer it" (null) so it
     * passes the nullable rule rather than failing the in:bios,uefi constraint.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('firmware') === '') {
            $this->merge(['firmware' => null]);
        }
    }
}
