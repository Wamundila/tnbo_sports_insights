<?php

namespace App\Http\Requests\Reports;

use App\Http\Requests\StrictFormRequest;
use Illuminate\Validation\Rule;

class OverviewReportRequest extends StrictFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'service' => ['nullable', 'string', Rule::in(config('insights.allowed_services'))],
            'surface' => ['nullable', 'string', 'max:100'],
            'campaign_id' => ['nullable', 'string', 'max:100'],
        ];
    }
}
