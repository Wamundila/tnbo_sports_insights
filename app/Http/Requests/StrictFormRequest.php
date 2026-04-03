<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

abstract class StrictFormRequest extends FormRequest
{
    protected function rejectUnknownKeys(Validator $validator, array $payload, array $allowedKeys, string $attributePath = ''): void
    {
        $unknownKeys = array_diff(array_keys($payload), $allowedKeys);

        foreach ($unknownKeys as $unknownKey) {
            $attribute = $attributePath === '' ? $unknownKey : "{$attributePath}.{$unknownKey}";

            $validator->errors()->add($attribute, 'The '.$attribute.' field is not allowed.');
        }
    }

    protected function rejectUnknownKeysInCollection(
        Validator $validator,
        string $collectionKey,
        array $allowedKeys
    ): void {
        $items = Arr::wrap($this->input($collectionKey, []));

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $this->rejectUnknownKeys($validator, $item, $allowedKeys, "{$collectionKey}.{$index}");
        }
    }
}
