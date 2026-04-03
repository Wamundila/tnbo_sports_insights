@extends('layouts.admin', [
    'title' => 'Campaigns | TNBO Insights Admin',
    'heading' => 'Campaigns',
    'subheading' => 'Manage active sponsor campaigns, schedule windows, and targeting metadata.',
])

@section('content')
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="panel-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-label mb-0">{{ $editing ? 'Edit Campaign' : 'Create Campaign' }}</div>
                    @if ($editing)
                        <a href="{{ route('admin.campaigns.index') }}" class="btn btn-sm btn-outline-secondary">Cancel edit</a>
                    @endif
                </div>
                <form method="POST" action="{{ $editing ? route('admin.campaigns.update', $editing) : route('admin.campaigns.store') }}" class="d-grid gap-3">
                    @csrf
                    @if ($editing)
                        @method('PUT')
                    @endif
                    <div>
                        <label class="form-label">Sponsor</label>
                        <select class="form-select" name="sponsor_id" required>
                            @foreach ($sponsors as $sponsor)
                                <option value="{{ $sponsor->id }}" @selected((int) old('sponsor_id', $editing?->sponsor_id) === $sponsor->id)>{{ $sponsor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="form-label">Code</label><input class="form-control" name="code" value="{{ old('code', $editing?->code) }}" placeholder="cmp_2026_001" required></div>
                    <div><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name', $editing?->name) }}" required></div>
                    <div><label class="form-label">Objective</label><input class="form-control" name="objective" value="{{ old('objective', $editing?->objective) }}" placeholder="awareness"></div>
                    <div>
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            @foreach (['draft', 'active', 'paused', 'completed'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $editing?->status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Start</label><input class="form-control" type="datetime-local" name="start_at" value="{{ old('start_at', optional($editing?->start_at)->format('Y-m-d\\TH:i')) }}"></div>
                        <div class="col-md-6"><label class="form-label">End</label><input class="form-control" type="datetime-local" name="end_at" value="{{ old('end_at', optional($editing?->end_at)->format('Y-m-d\\TH:i')) }}"></div>
                    </div>
                    <div><label class="form-label">Priority</label><input class="form-control" type="number" min="0" name="priority" value="{{ old('priority', $editing?->priority ?? 0) }}" required></div>
                    <div><label class="form-label">Budget Notes</label><textarea class="form-control" name="budget_notes" rows="2">{{ old('budget_notes', $editing?->budget_notes) }}</textarea></div>
                    <div><label class="form-label">Targeting JSON</label><textarea class="form-control" name="targeting_json" rows="3" placeholder='{"competition_id":["super_league_2026"]}'>{{ old('targeting_json', $editing && $editing->targeting_json ? json_encode($editing->targeting_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea></div>
                    <div><label class="form-label">Frequency Cap JSON</label><textarea class="form-control" name="frequency_cap_json" rows="2" placeholder='{"per_user_per_day":3}'>{{ old('frequency_cap_json', $editing && $editing->frequency_cap_json ? json_encode($editing->frequency_cap_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea></div>
                    <div><label class="form-label">Reporting Label</label><input class="form-control" name="reporting_label" value="{{ old('reporting_label', $editing?->reporting_label) }}"></div>
                    <button class="btn btn-primary">{{ $editing ? 'Save campaign' : 'Create campaign' }}</button>
                </form>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="panel-card">
                <form method="GET" class="row g-3 align-items-end mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Sponsor</label>
                        <select class="form-select" name="sponsor_id">
                            <option value="">All sponsors</option>
                            @foreach ($sponsors as $sponsor)
                                <option value="{{ $sponsor->id }}" @selected((string) ($filters['sponsor_id'] ?? '') === (string) $sponsor->id)>{{ $sponsor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All statuses</option>
                            @foreach (['draft', 'active', 'paused', 'completed'] as $status)
                                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
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
                            <th>Sponsor</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($campaigns as $campaign)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $campaign->name }}</div>
                                    <div class="text-secondary small"><code>{{ $campaign->code }}</code></div>
                                </td>
                                <td>{{ $campaign->sponsor?->name }}</td>
                                <td class="small">
                                    <div>{{ optional($campaign->start_at)->format('Y-m-d H:i') ?: 'Open' }}</div>
                                    <div class="text-secondary">{{ optional($campaign->end_at)->format('Y-m-d H:i') ?: 'No end date' }}</div>
                                </td>
                                <td><span class="badge badge-soft">{{ $campaign->status }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">No campaigns created yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">{{ $campaigns->links() }}</div>
            </div>
        </div>
    </div>
@endsection
