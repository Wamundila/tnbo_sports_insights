<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Sponsor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(Request $request): View
    {
        $query = Campaign::query()->with('sponsor')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('sponsor_id')) {
            $query->where('sponsor_id', $request->integer('sponsor_id'));
        }

        return view('admin.campaigns.index', $this->viewData($query->paginate(15)->withQueryString(), $request));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        Campaign::query()->create($data);

        return redirect()
            ->route('admin.campaigns.index')
            ->with('status', 'Campaign created.');
    }

    public function edit(Request $request, Campaign $campaign): View
    {
        $query = Campaign::query()->with('sponsor')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('sponsor_id')) {
            $query->where('sponsor_id', $request->integer('sponsor_id'));
        }

        return view('admin.campaigns.index', $this->viewData(
            $query->paginate(15)->withQueryString(),
            $request,
            $campaign
        ));
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update($this->validatedData($request, $campaign));

        return redirect()
            ->route('admin.campaigns.edit', $campaign)
            ->with('status', 'Campaign updated.');
    }

    private function validatedData(Request $request, ?Campaign $campaign = null): array
    {
        $data = $request->validate([
            'sponsor_id' => ['required', 'integer', 'exists:sponsors,id'],
            'code' => ['required', 'string', 'max:100', Rule::unique('campaigns', 'code')->ignore($campaign)],
            'name' => ['required', 'string', 'max:255'],
            'objective' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:40'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'priority' => ['required', 'integer', 'min:0'],
            'budget_notes' => ['nullable', 'string'],
            'targeting_json' => ['nullable', 'string'],
            'frequency_cap_json' => ['nullable', 'string'],
            'reporting_label' => ['nullable', 'string', 'max:255'],
        ]);

        $data['targeting_json'] = $this->decodeJsonField($data['targeting_json'] ?? null, 'targeting_json', 'Targeting JSON must be valid JSON.');
        $data['frequency_cap_json'] = $this->decodeJsonField($data['frequency_cap_json'] ?? null, 'frequency_cap_json', 'Frequency cap JSON must be valid JSON.');

        return $data;
    }

    private function viewData($campaigns, Request $request, ?Campaign $editing = null): array
    {
        return [
            'campaigns' => $campaigns,
            'sponsors' => Sponsor::query()->orderBy('name')->get(),
            'filters' => $request->only('status', 'sponsor_id'),
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
}
