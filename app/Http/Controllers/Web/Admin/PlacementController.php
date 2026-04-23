<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Placement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlacementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Placement::query()->latest('id');

        if ($request->filled('service')) {
            $query->where('service', $request->string('service'));
        }

        if ($request->filled('surface')) {
            $query->where('surface', $request->string('surface'));
        }

        return view('admin.placements.index', $this->viewData($query->paginate(15)->withQueryString(), $request));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['is_active'] = $request->boolean('is_active');

        Placement::query()->create($data);

        return redirect()
            ->route('admin.placements.index')
            ->with('status', 'Placement created.');
    }

    public function edit(Request $request, Placement $placement): View
    {
        $query = Placement::query()->latest('id');

        if ($request->filled('service')) {
            $query->where('service', $request->string('service'));
        }

        if ($request->filled('surface')) {
            $query->where('surface', $request->string('surface'));
        }

        return view('admin.placements.index', $this->viewData(
            $query->paginate(15)->withQueryString(),
            $request,
            $placement
        ));
    }

    public function update(Request $request, Placement $placement): RedirectResponse
    {
        $data = $this->validatedData($request, $placement);
        $data['is_active'] = $request->boolean('is_active');

        $placement->update($data);

        return redirect()
            ->route('admin.placements.edit', $placement)
            ->with('status', 'Placement updated.');
    }

    private function validatedData(Request $request, ?Placement $placement = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:100', Rule::unique('placements', 'code')->ignore($placement)],
            'name' => ['required', 'string', 'max:255'],
            'service' => ['required', 'string', Rule::in(config('insights.allowed_services'))],
            'surface' => ['required', 'string', 'max:100'],
            'block_type' => ['required', 'string', Rule::in(array_keys(config('insights.placement_block_types')))],
            'allowed_creative_types' => ['nullable', 'array'],
            'allowed_creative_types.*' => ['string', Rule::in(array_keys(config('insights.creative_types')))],
            'position_hint' => ['nullable', 'string', 'max:60'],
            'max_creatives_per_response' => ['required', 'integer', 'min:1', 'max:10'],
            'description' => ['nullable', 'string'],
        ]);

        $data['allowed_creative_types'] = array_values(array_unique($data['allowed_creative_types'] ?? []));

        return $data;
    }

    private function viewData($placements, Request $request, ?Placement $editing = null): array
    {
        return [
            'placements' => $placements,
            'filters' => $request->only('service', 'surface'),
            'allowedServices' => config('insights.allowed_services'),
            'placementBlockTypes' => config('insights.placement_block_types'),
            'creativeTypes' => config('insights.creative_types'),
            'editing' => $editing,
        ];
    }
}
