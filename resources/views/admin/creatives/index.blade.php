@extends('layouts.admin', [
    'title' => 'Creatives | TNBO Insights Admin',
    'heading' => 'Creatives',
    'subheading' => 'Attach campaign assets and CTA payloads for placement resolution.',
])

@section('content')
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="panel-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-label mb-0">{{ $editing ? 'Edit Creative' : 'Create Creative' }}</div>
                    @if ($editing)
                        <a href="{{ route('admin.creatives.index') }}" class="btn btn-sm btn-outline-secondary">Cancel edit</a>
                    @endif
                </div>
                <form method="POST" action="{{ $editing ? route('admin.creatives.update', $editing) : route('admin.creatives.store') }}" class="d-grid gap-3" enctype="multipart/form-data">
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
                    <div><label class="form-label">Code</label><input class="form-control" name="code" value="{{ old('code', $editing?->code) }}" placeholder="creative_01" required></div>
                    <div>
                        <label class="form-label">Creative Type</label>
                        <select class="form-select" name="creative_type" required>
                            @foreach ($creativeTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('creative_type', $editing?->creative_type ?? 'image_banner') === $value)>{{ $label }} ({{ $value }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="form-label">Label Text</label><input class="form-control" name="label_text" value="{{ old('label_text', $editing?->label_text ?? 'Sponsored') }}"></div>
                    <div><label class="form-label">Title</label><input class="form-control" name="title" value="{{ old('title', $editing?->title) }}"></div>
                    <div><label class="form-label">Body</label><textarea class="form-control" name="body" rows="3">{{ old('body', $editing?->body) }}</textarea></div>
                    <div>
                        <label class="form-label">Image Upload</label>
                        <input class="form-control" type="file" name="image_file" accept="image/*">
                        @if ($editing?->image_url)
                            <div class="mt-2 d-flex align-items-center gap-3">
                                <img src="{{ $editing->image_url }}" alt="Creative image preview" class="rounded-3 border" style="width: 96px; height: 72px; object-fit: cover;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="remove_image" id="remove_image">
                                    <label class="form-check-label" for="remove_image">Remove current image</label>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div>
                        <label class="form-label">Logo Upload</label>
                        <input class="form-control" type="file" name="logo_file" accept="image/*">
                        @if ($editing?->logo_url)
                            <div class="mt-2 d-flex align-items-center gap-3">
                                <img src="{{ $editing->logo_url }}" alt="Creative logo preview" class="rounded-3 border bg-white p-2" style="width: 96px; height: 72px; object-fit: contain;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" name="remove_logo" id="remove_logo">
                                    <label class="form-check-label" for="remove_logo">Remove current logo</label>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div><label class="form-label">CTA Text</label><input class="form-control" name="cta_text" value="{{ old('cta_text', $editing?->cta_text) }}"></div>
                    <div><label class="form-label">CTA URL</label><input class="form-control" name="cta_url" value="{{ old('cta_url', $editing?->cta_url) }}"></div>
                    <div><label class="form-label">Metadata JSON</label><textarea class="form-control" name="metadata_json" rows="3">{{ old('metadata_json', $editing && $editing->metadata_json ? json_encode($editing->metadata_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea></div>
                    <div>
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            @foreach (['active', 'inactive'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $editing?->status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary">{{ $editing ? 'Save creative' : 'Create creative' }}</button>
                </form>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="panel-card">
                <form method="GET" class="row g-3 align-items-end mb-4">
                    <div class="col-md-8">
                        <label class="form-label">Campaign</label>
                        <select class="form-select" name="campaign_id">
                            <option value="">All campaigns</option>
                            @foreach ($campaigns as $campaign)
                                <option value="{{ $campaign->id }}" @selected((string) ($filters['campaign_id'] ?? '') === (string) $campaign->id)>{{ $campaign->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4"><button class="btn btn-outline-secondary w-100">Apply filter</button></div>
                </form>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Creative</th>
                            <th>Campaign</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($creatives as $creative)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $creative->title ?: 'Untitled creative' }}</div>
                                    <div class="text-secondary small"><code>{{ $creative->code }}</code></div>
                                </td>
                                <td>{{ $creative->campaign?->name }}</td>
                                <td>
                                    <div>{{ $creative->creative_type }}</div>
                                    <div class="text-secondary small">
                                        @if ($creative->image_url)
                                            Image
                                        @endif
                                        @if ($creative->image_url && $creative->logo_url)
                                            /
                                        @endif
                                        @if ($creative->logo_url)
                                            Logo
                                        @endif
                                        @if (! $creative->image_url && ! $creative->logo_url)
                                            No uploads
                                        @endif
                                    </div>
                                </td>
                                <td><span class="badge badge-soft">{{ $creative->status }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.creatives.edit', $creative) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">No creatives created yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">{{ $creatives->links() }}</div>
            </div>
        </div>
    </div>
@endsection
