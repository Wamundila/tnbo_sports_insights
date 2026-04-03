<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreEventBatchRequest extends StrictFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source' => ['nullable', 'string', 'max:100'],
            'schema_version' => ['required', 'integer', 'min:1'],
            'events' => ['required', 'array', 'min:1', 'max:1000'],
            'events.*.event_id' => ['required', 'string', 'max:100'],
            'events.*.event_name' => ['required', 'string', 'max:100'],
            'events.*.occurred_at' => ['required', 'date'],
            'events.*.service' => ['required', 'string', Rule::in(config('insights.allowed_services'))],
            'events.*.surface' => ['required', 'string', 'max:100'],
            'events.*.screen_name' => ['required', 'string', 'max:100'],
            'events.*.user_id' => ['nullable', 'string', 'max:100'],
            'events.*.anonymous_id' => ['required', 'string', 'max:100'],
            'events.*.session_id' => ['required', 'string', 'max:100'],
            'events.*.device_id' => ['nullable', 'string', 'max:150'],
            'events.*.platform' => ['required', 'string', Rule::in(config('insights.allowed_platforms'))],
            'events.*.app_version' => ['required', 'string', 'max:30'],
            'events.*.block_id' => ['nullable', 'string', 'max:100'],
            'events.*.block_type' => ['nullable', 'string', 'max:100'],
            'events.*.placement_id' => ['nullable', 'string', 'max:100'],
            'events.*.position_index' => ['nullable', 'integer'],
            'events.*.content_id' => $this->identifierRule(150),
            'events.*.content_type' => ['nullable', 'string', 'max:100'],
            'events.*.campaign_id' => ['nullable', 'string', 'max:100'],
            'events.*.creative_id' => ['nullable', 'string', 'max:100'],
            'events.*.match_id' => $this->identifierRule(),
            'events.*.competition_id' => $this->identifierRule(),
            'events.*.team_id' => $this->identifierRule(),
            'events.*.metadata' => ['nullable', 'array'],
            'events.*.properties' => ['nullable', 'array'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->rejectUnknownKeys($validator, $this->all(), [
                'source',
                'schema_version',
                'events',
            ]);

            $this->rejectUnknownKeysInCollection($validator, 'events', [
                'event_id',
                'event_name',
                'occurred_at',
                'service',
                'surface',
                'screen_name',
                'user_id',
                'anonymous_id',
                'session_id',
                'device_id',
                'platform',
                'app_version',
                'block_id',
                'block_type',
                'placement_id',
                'position_index',
                'content_id',
                'content_type',
                'campaign_id',
                'creative_id',
                'match_id',
                'competition_id',
                'team_id',
                'metadata',
                'properties',
            ]);
        });
    }

    private function identifierRule(int $max = 100): array
    {
        return [
            'nullable',
            function (string $attribute, mixed $value, Closure $fail) use ($max): void {
                if (! is_string($value) && ! is_int($value)) {
                    $fail("The {$attribute} field must be a string or integer identifier.");

                    return;
                }

                if (mb_strlen((string) $value) > $max) {
                    $fail("The {$attribute} field must not be greater than {$max} characters.");
                }
            },
        ];
    }
}
