<?php

namespace Ruff\Http\Requests\Admin;

use Ruff\Models\Server;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class VpsServerFormRequest extends AdminFormRequest
{
    /**
     * Rules applied when creating a VPS (QEMU/KVM) server. A VPS is backed by a
     * Pup template instead of a nest/egg, so the egg-specific fields are dropped
     * and a Pup is required. The startup command and base image are derived from
     * the Pup server-side, never from user input.
     */
    public function rules(): array
    {
        $rules = Server::getRules();
        $rules['description'][] = 'nullable';
        $rules['pup_id'] = 'required|exists:pups,id';

        unset($rules['egg_id'], $rules['nest_id'], $rules['startup'], $rules['image']);

        return $rules;
    }

    /**
     * Ensure the chosen allocation actually belongs to the chosen node and is
     * free, mirroring the game-server form's allocation safety checks.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $validator->sometimes('allocation_id', [
                'required',
                'numeric',
                'bail',
                Rule::exists('allocations', 'id')->where(function ($query) {
                    $query->where('node_id', $this->input('node_id'));
                    $query->whereNull('server_id');
                }),
            ], fn () => true);

            $validator->sometimes('allocation_additional.*', [
                'sometimes',
                'required',
                'numeric',
                Rule::exists('allocations', 'id')->where(function ($query) {
                    $query->where('node_id', $this->input('node_id'));
                    $query->whereNull('server_id');
                }),
            ], fn () => true);
        });
    }
}
