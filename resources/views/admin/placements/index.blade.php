@extends('layouts.admin', [
    'title' => 'Placements | TNBO Insights Admin',
    'heading' => 'Placements',
    'subheading' => 'Define inventory slots that BFF can request for sponsor insertion.',
])

@section('content')
    @php
        $selectedBlockType = old('block_type', $editing?->block_type ?? 'sponsor_card');
        $selectedCreativeTypes = old('allowed_creative_types', $editing?->allowed_creative_types ?? ['image_banner']);
        $selectedCreativeTypes = is_array($selectedCreativeTypes) ? $selectedCreativeTypes : [$selectedCreativeTypes];
    @endphp

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="panel-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-label mb-0">{{ $editing ? 'Edit Placement' : 'Create Placement' }}</div>
                    @if ($editing)
                        <a href="{{ route('admin.placements.index') }}" class="btn btn-sm btn-outline-secondary">Cancel edit</a>
                    @endif
                </div>
                <form method="POST" action="{{ $editing ? route('admin.placements.update', $editing) : route('admin.placements.store') }}" class="d-grid gap-3">
                    @csrf
                    @if ($editing)
                        @method('PUT')
                    @endif
                    <div><label class="form-label">Code</label><input class="form-control" name="code" value="{{ old('code', $editing?->code) }}" placeholder="home_inline_1" required></div>
                    <div><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name', $editing?->name) }}" required></div>
                    <div>
                        <label class="form-label">Service</label>
                        <select class="form-select" name="service" required>
                            @foreach ($allowedServices as $service)
                                <option value="{{ $service }}" @selected(old('service', $editing?->service) === $service)>{{ $service }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="form-label">Surface</label><input class="form-control" name="surface" value="{{ old('surface', $editing?->surface) }}" placeholder="home_page" required></div>
                    <div>
                        <label class="form-label">Block Type</label>
                        <select class="form-select" name="block_type" required>
                            @foreach ($placementBlockTypes as $value => $label)
                                <option value="{{ $value }}" @selected($selectedBlockType === $value)>{{ $label }} ({{ $value }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">Frontend block template BFF/app should render for this placement.</div>
                    </div>
                    <div>
                        <label class="form-label">Allowed Creative Types</label>
                        <div class="vstack gap-2">
                            @foreach ($creativeTypes as $value => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="allowed_creative_types[]" value="{{ $value }}" id="creativeType{{ $loop->index }}" @checked(in_array($value, $selectedCreativeTypes, true))>
                                    <label class="form-check-label" for="creativeType{{ $loop->index }}">{{ $label }} ({{ $value }})</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text">Compatibility list for creatives that may be served into this slot.</div>
                    </div>
                    <div><label class="form-label">Position Hint</label><input class="form-control" name="position_hint" value="{{ old('position_hint', $editing?->position_hint) }}" placeholder="inline_1"></div>
                    <div><label class="form-label">Max Creatives Per Response</label><input class="form-control" type="number" min="1" max="10" name="max_creatives_per_response" value="{{ old('max_creatives_per_response', $editing?->max_creatives_per_response ?? 1) }}" required></div>
                    <div><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3">{{ old('description', $editing?->description) }}</textarea></div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="placementActive" @checked(old('is_active', $editing?->is_active ?? true))>
                        <label class="form-check-label" for="placementActive">Active placement</label>
                    </div>
                    <button class="btn btn-primary">{{ $editing ? 'Save placement' : 'Create placement' }}</button>
                </form>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="panel-card">
                <form method="GET" class="row g-3 align-items-end mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Service</label>
                        <select class="form-select" name="service">
                            <option value="">All services</option>
                            @foreach ($allowedServices as $service)
                                <option value="{{ $service }}" @selected(($filters['service'] ?? '') === $service)>{{ $service }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Surface</label>
                        <input class="form-control" name="surface" value="{{ $filters['surface'] ?? '' }}" placeholder="match_center_page">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-outline-secondary">Apply filters</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Code</th>
                            <th>Service / Surface</th>
                            <th>Block Type</th>
                            <th>Allowed Creatives</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($placements as $placement)
                            <tr>
                                <td><code>{{ $placement->code }}</code></td>
                                <td>
                                    <div>{{ $placement->service }}</div>
                                    <div class="text-secondary small">{{ $placement->surface }}</div>
                                </td>
                                <td>{{ $placement->block_type }}</td>
                                <td>
                                    @forelse ($placement->allowed_creative_types ?? [] as $creativeType)
                                        <span class="badge text-bg-light border">{{ $creativeType }}</span>
                                    @empty
                                        <span class="text-secondary small">Any configured type</span>
                                    @endforelse
                                </td>
                                <td><span class="badge badge-soft">{{ $placement->is_active ? 'active' : 'inactive' }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.placements.edit', $placement) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-secondary py-4">No placements created yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">{{ $placements->links() }}</div>
            </div>
        </div>
    </div>
@endsection
