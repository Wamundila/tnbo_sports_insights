<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\StrictFormRequest;
use Illuminate\Validation\Rule;

class StoreCampaignTargetRequest extends StrictFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'placement_id' => ['required', 'integer', 'exists:placements,id'],
            'service' => ['nullable', 'string', Rule::in(config('insights.allowed_services'))],
            'surface' => ['nullable', 'string', 'max:100'],
            'priority' => ['sometimes', 'integer', 'min:0'],
            'weight' => ['sometimes', 'integer', 'min:1'],
            'max_impressions' => ['nullable', 'integer', 'min:1'],
            'max_clicks' => ['nullable', 'integer', 'min:1'],
            'constraints_json' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
