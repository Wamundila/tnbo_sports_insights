<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\StrictFormRequest;
use Illuminate\Validation\Rule;

class StoreCampaignCreativeRequest extends StrictFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'code' => ['required', 'string', 'max:100'],
            'creative_type' => ['required', 'string', Rule::in(['image_banner'])],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'label_text' => ['nullable', 'string', 'max:100'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'logo_url' => ['nullable', 'url', 'max:255'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'audio_url' => ['nullable', 'url', 'max:255'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'url', 'max:255'],
            'metadata_json' => ['nullable', 'array'],
            'status' => ['sometimes', 'string', 'max:40'],
        ];
    }
}
