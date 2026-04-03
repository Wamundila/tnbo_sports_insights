@extends('layouts.admin', [
    'title' => 'Targets | TNBO Insights Admin',
    'heading' => 'Campaign Targets',
    'subheading' => 'Bind campaigns to placements and tune resolution priority.',
])

@section('content')
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="panel-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-label mb-0">{{ $editing ? 'Edit Target' : 'Create Target' }}</div>
                    @if ($editing)
                        <a href="{{ route('admin.targets.index') }}" class="btn btn-sm btn-outline-secondary">Cancel edit</a>
                    @endif
                </div>
                <form method="POST" action="{{ $editing ? route('admin.targets.update', $editing) : route('admin.targets.store') }}" class="d-grid gap-3">
                    @csrf
                    @if ($editing)
                        @method('PUT')
                    @endif
                    <div>
                        <label class="form-label">Campaign</label>
                        <select class="form-select" name="campaign_id" required>
                            @foreach ($campaigns as $campaign)
                                <option value="{{ $campaign->id }}" @selected((int) old('campaign_id', $editing?->campaign_id) === $campaign->id)>{{ $campaign->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Placement</label>
                        <select class="form-select" name="placement_id" required>
                            @foreach ($placements as $placement)
                                <option value="{{ $placement->id }}" @selected((int) old('placement_id', $editing?->placement_id) === $placement->id)>{{ $placement->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Service Override</label>
                        <select class="form-select" name="service">
                            <option value="">Use placement service</option>
                            @foreach ($allowedServices as $service)
                                <option value="{{ $service }}" @selected(old('service', $editing?->service) === $service)>{{ $service }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="form-label">Surface Override</label><input class="form-control" name="surface" value="{{ old('surface', $editing?->surface) }}"></div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Priority</label><input class="form-control" type="number" min="0" name="priority" value="{{ old('priority', $editing?->priority ?? 0) }}" required></div>
                        <div class="col-md-6"><label class="form-label">Weight</label><input class="form-control" type="number" min="1" name="weight" value="{{ old('weight', $editing?->weight ?? 1) }}" required></div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Max Impressions</label><input class="form-control" type="number" min="1" name="max_impressions" value="{{ old('max_impressions', $editing?->max_impressions) }}"></div>
                        <div class="col-md-6"><label class="form-label">Max Clicks</label><input class="form-control" type="number" min="1" name="max_clicks" value="{{ old('max_clicks', $editing?->max_clicks) }}"></div>
                    </div>
                    <div><label class="form-label">Constraints JSON</label><textarea class="form-control" name="constraints_json" rows="3">{{ old('constraints_json', $editing && $editing->constraints_json ? json_encode($editing->constraints_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea></div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="targetActive" @checked(old('is_active', $editing?->is_active ?? true))>
                        <label class="form-check-label" for="targetActive">Active target</label>
                    </div>
                    <button class="btn btn-primary">{{ $editing ? 'Save target' : 'Create target' }}</button>
                </form>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="panel-card">
                <form method="GET" class="row g-3 align-items-end mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Campaign</label>
                        <select class="form-select" name="campaign_id">
                            <option value="">All campaigns</option>
                            @foreach ($campaigns as $campaign)
                                <option value="{{ $campaign->id }}" @selected((string) ($filters['campaign_id'] ?? '') === (string) $campaign->id)>{{ $campaign->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Placement</label>
                        <select class="form-select" name="placement_id">
                            <option value="">All placements</option>
                            @foreach ($placements as $placement)
                                <option value="{{ $placement->id }}" @selected((string) ($filters['placement_id'] ?? '') === (string) $placement->id)>{{ $placement->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12"><button class="btn btn-outline-secondary">Apply filters</button></div>
                </form>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Placement</th>
                            <th>Scope</th>
                            <th>Priority</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($targets as $target)
                            <tr>
                                <td>{{ $target->campaign?->name }}</td>
                                <td><code>{{ $target->placement?->code }}</code></td>
                                <td>
                                    <div>{{ $target->service ?: 'placement default' }}</div>
                                    <div class="text-secondary small">{{ $target->surface ?: 'all matching surfaces' }}</div>
                                </td>
                                <td>{{ $target->priority }} / {{ $target->weight }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.targets.edit', $target) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">No targets created yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">{{ $targets->links() }}</div>
            </div>
        </div>
    </div>
@endsection
