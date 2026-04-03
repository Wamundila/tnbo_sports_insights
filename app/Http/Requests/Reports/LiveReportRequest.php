<?php

namespace App\Http\Requests\Reports;

use App\Http\Requests\StrictFormRequest;
use Illuminate\Validation\Rule;

class LiveReportRequest extends StrictFormRequest
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
            'service' => ['nullable', 'string', Rule::in(['media', 'match_center'])],
            'match_id' => ['nullable', 'string', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
