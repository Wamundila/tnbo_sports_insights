<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignCreative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CampaignCreativeController extends Controller
{
    public function index(Request $request): View
    {
        $query = CampaignCreative::query()->with('campaign')->latest('id');

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->integer('campaign_id'));
        }

        return view('admin.creatives.index', $this->viewData($query->paginate(15)->withQueryString(), $request));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        CampaignCreative::query()->create($data);

        return redirect()
            ->route('admin.creatives.index')
            ->with('status', 'Creative created.');
    }

    public function edit(Request $request, CampaignCreative $creative): View
    {
        $query = CampaignCreative::query()->with('campaign')->latest('id');

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->integer('campaign_id'));
        }

        return view('admin.creatives.index', $this->viewData(
            $query->paginate(15)->withQueryString(),
            $request,
            $creative
        ));
    }

    public function update(Request $request, CampaignCreative $creative): RedirectResponse
    {
        $creative->update($this->validatedData($request, $creative));

        return redirect()
            ->route('admin.creatives.edit', $creative)
            ->with('status', 'Creative updated.');
    }

    private function validatedData(Request $request, ?CampaignCreative $creative = null): array
    {
        $data = $request->validate([
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'code' => ['required', 'string', 'max:100', Rule::unique('campaign_creatives', 'code')->ignore($creative)],
            'creative_type' => ['required', 'string', Rule::in(['image_banner'])],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'label_text' => ['nullable', 'string', 'max:100'],
            'image_file' => ['nullable', 'file', 'image', 'max:5120'],
            'logo_file' => ['nullable', 'file', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'remove_logo' => ['nullable', 'boolean'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'audio_url' => ['nullable', 'url', 'max:255'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'url', 'max:255'],
            'metadata_json' => ['nullable', 'string'],
            'status' => ['required', 'string', 'max:40'],
        ]);

        $data['metadata_json'] = $this->decodeJsonField($data['metadata_json'] ?? null, 'metadata_json', 'Metadata JSON must be valid JSON.');
        $data['image_url'] = $this->resolveAssetUpload(
            $request,
            'image_file',
            $creative?->image_url,
            (bool) ($data['remove_image'] ?? false),
            'images'
        );
        $data['logo_url'] = $this->resolveAssetUpload(
            $request,
            'logo_file',
            $creative?->logo_url,
            (bool) ($data['remove_logo'] ?? false),
            'logos'
        );

        unset($data['image_file'], $data['logo_file'], $data['remove_image'], $data['remove_logo']);

        return $data;
    }

    private function viewData($creatives, Request $request, ?CampaignCreative $editing = null): array
    {
        return [
            'creatives' => $creatives,
            'campaigns' => Campaign::query()->orderBy('name')->get(),
            'filters' => $request->only('campaign_id'),
            'editing' => $editing,
        ];
    }

    private function decodeJsonField(?string $value, string $field, string $message): ?array
    {
        if (! filled($value)) {
            return null;
        }

        try {
            return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw ValidationException::withMessages([
                $field => $message,
            ]);
        }
    }

    private function resolveAssetUpload(
        Request $request,
        string $field,
        ?string $currentUrl,
        bool $removeExisting,
        string $directory
    ): ?string {
        if ($request->hasFile($field)) {
            $this->deleteStoredAsset($currentUrl);

            $storedPath = $request->file($field)->storeAs(
                sprintf('creative-assets/%s', $directory),
                sprintf('%s.%s', Str::uuid(), $request->file($field)->extension()),
                'public'
            );

            return Storage::disk('public')->url($storedPath);
        }

        if ($removeExisting) {
            $this->deleteStoredAsset($currentUrl);

            return null;
        }

        return $currentUrl;
    }

    private function deleteStoredAsset(?string $url): void
    {
        if (! filled($url)) {
            return;
        }

        $publicPrefix = Storage::disk('public')->url('');

        if (! Str::startsWith($url, $publicPrefix)) {
            return;
        }

        $relativePath = ltrim(Str::after($url, $publicPrefix), '/');

        if ($relativePath !== '') {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
