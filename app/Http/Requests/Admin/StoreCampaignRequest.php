<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\StrictFormRequest;

class StoreCampaignRequest extends StrictFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sponsor_id' => ['required', 'integer', 'exists:sponsors,id'],
            'code' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'objective' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'string', 'max:40'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'priority' => ['sometimes', 'integer', 'min:0'],
            'budget_notes' => ['nullable', 'string'],
            'targeting_json' => ['nullable', 'array'],
            'frequency_cap_json' => ['nullable', 'array'],
            'reporting_label' => ['nullable', 'string', 'max:255'],
        ];
    }
}
