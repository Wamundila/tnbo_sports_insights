@extends('layouts.admin', [
    'title' => 'Sponsors | TNBO Insights Admin',
    'heading' => 'Sponsors',
    'subheading' => 'Manage sponsor records used by campaigns and reporting.',
])

@section('content')
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="panel-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-label mb-0">{{ $editing ? 'Edit Sponsor' : 'Create Sponsor' }}</div>
                    @if ($editing)
                        <a href="{{ route('admin.sponsors.index') }}" class="btn btn-sm btn-outline-secondary">Cancel edit</a>
                    @endif
                </div>
                <form method="POST" action="{{ $editing ? route('admin.sponsors.update', $editing) : route('admin.sponsors.store') }}" class="d-grid gap-3">
                    @csrf
                    @if ($editing)
                        @method('PUT')
                    @endif
                    <div>
                        <label class="form-label">Code</label>
                        <input class="form-control" name="code" value="{{ old('code', $editing?->code) }}" placeholder="zamtel" required>
                    </div>
                    <div>
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" value="{{ old('name', $editing?->name) }}" placeholder="Zamtel" required>
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            @foreach (['active', 'inactive'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $editing?->status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Website URL</label>
                        <input class="form-control" name="website_url" value="{{ old('website_url', $editing?->website_url) }}" placeholder="https://example.com">
                    </div>
                    <div>
                        <label class="form-label">Contact Name</label>
                        <input class="form-control" name="contact_name" value="{{ old('contact_name', $editing?->contact_name) }}">
                    </div>
                    <div>
                        <label class="form-label">Contact Email</label>
                        <input class="form-control" type="email" name="contact_email" value="{{ old('contact_email', $editing?->contact_email) }}">
                    </div>
                    <div>
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="4">{{ old('notes', $editing?->notes) }}</textarea>
                    </div>
                    <button class="btn btn-primary">{{ $editing ? 'Save sponsor' : 'Create sponsor' }}</button>
                </form>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="panel-card">
                <form method="GET" class="row g-3 align-items-end mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All statuses</option>
                            @foreach (['active', 'inactive'] as $status)
                                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-secondary w-100">Apply filter</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Contact</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($sponsors as $sponsor)
                            <tr>
                                <td><code>{{ $sponsor->code }}</code></td>
                                <td>{{ $sponsor->name }}</td>
                                <td><span class="badge badge-soft">{{ $sponsor->status }}</span></td>
                                <td>
                                    <div>{{ $sponsor->contact_name ?: 'No contact' }}</div>
                                    <div class="text-secondary small">{{ $sponsor->contact_email ?: 'No email' }}</div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.sponsors.edit', $sponsor) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">No sponsors created yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">{{ $sponsors->links() }}</div>
            </div>
        </div>
    </div>
@endsection
