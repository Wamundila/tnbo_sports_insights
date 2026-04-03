<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ResolvePlacementsRequest extends StrictFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'string', 'max:100'],
            'anonymous_id' => ['required', 'string', 'max:100'],
            'session_id' => ['required', 'string', 'max:100'],
            'platform' => ['required', 'string', Rule::in(config('insights.allowed_platforms'))],
            'service' => ['required', 'string', Rule::in(config('insights.allowed_services'))],
            'surface' => ['required', 'string', 'max:100'],
            'screen_name' => ['required', 'string', 'max:100'],
            'context' => ['nullable', 'array'],
            'placements' => ['required', 'array', 'min:1', 'max:25'],
            'placements.*' => ['required', 'string', 'max:100', 'distinct'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->rejectUnknownKeys($validator, $this->all(), [
                'user_id',
                'anonymous_id',
                'session_id',
                'platform',
                'service',
                'surface',
                'screen_name',
                'context',
                'placements',
            ]);
        });
    }
}
