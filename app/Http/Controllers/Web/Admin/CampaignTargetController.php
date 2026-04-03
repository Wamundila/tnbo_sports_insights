<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignTarget;
use App\Models\Placement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CampaignTargetController extends Controller
{
    public function index(Request $request): View
    {
        $query = CampaignTarget::query()->with(['campaign', 'placement'])->latest('id');

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->integer('campaign_id'));
        }

        if ($request->filled('placement_id')) {
            $query->where('placement_id', $request->integer('placement_id'));
        }

        return view('admin.targets.index', $this->viewData($query->paginate(15)->withQueryString(), $request));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['is_active'] = $request->boolean('is_active');

        CampaignTarget::query()->create($data);

        return redirect()
            ->route('admin.targets.index')
            ->with('status', 'Campaign target created.');
    }

    public function edit(Request $request, CampaignTarget $target): View
    {
        $query = CampaignTarget::query()->with(['campaign', 'placement'])->latest('id');

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->integer('campaign_id'));
        }

        if ($request->filled('placement_id')) {
            $query->where('placement_id', $request->integer('placement_id'));
        }

        return view('admin.targets.index', $this->viewData(
            $query->paginate(15)->withQueryString(),
            $request,
            $target
        ));
    }

    public function update(Request $request, CampaignTarget $target): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['is_active'] = $request->boolean('is_active');

        $target->update($data);

        return redirect()
            ->route('admin.targets.edit', $target)
            ->with('status', 'Campaign target updated.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'placement_id' => ['required', 'integer', 'exists:placements,id'],
            'service' => ['nullable', 'string', Rule::in(config('insights.allowed_services'))],
            'surface' => ['nullable', 'string', 'max:100'],
            'priority' => ['required', 'integer', 'min:0'],
            'weight' => ['required', 'integer', 'min:1'],
            'max_impressions' => ['nullable', 'integer', 'min:1'],
            'max_clicks' => ['nullable', 'integer', 'min:1'],
            'constraints_json' => ['nullable', 'string'],
        ]);

        $data['constraints_json'] = $this->decodeJsonField($data['constraints_json'] ?? null, 'constraints_json', 'Constraints JSON must be valid JSON.');

        return $data;
    }

    private function viewData($targets, Request $request, ?CampaignTarget $editing = null): array
    {
        return [
            'targets' => $targets,
            'campaigns' => Campaign::query()->orderBy('name')->get(),
            'placements' => Placement::query()->orderBy('code')->get(),
            'allowedServices' => config('insights.allowed_services'),
            'filters' => $request->only('campaign_id', 'placement_id'),
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
