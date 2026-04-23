<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\StrictFormRequest;
use Illuminate\Validation\Rule;

class StorePlacementRequest extends StrictFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'service' => ['required', 'string', Rule::in(config('insights.allowed_services'))],
            'surface' => ['required', 'string', 'max:100'],
            'block_type' => ['required', 'string', Rule::in(array_keys(config('insights.placement_block_types')))],
            'allowed_creative_types' => ['nullable', 'array'],
            'allowed_creative_types.*' => ['string', Rule::in(array_keys(config('insights.creative_types')))],
            'position_hint' => ['nullable', 'string', 'max:60'],
            'max_creatives_per_response' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'default_rules' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
        ];
    }
}
