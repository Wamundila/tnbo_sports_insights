@extends('layouts.admin', [
    'title' => 'System Logs | TNBO Insights Admin',
    'heading' => 'System Logs',
    'subheading' => 'View, edit, or delete the Laravel application log file.',
])

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="panel-card">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
                    <div>
                        <div class="section-label mb-1">Laravel Log</div>
                        <div class="fw-semibold">{{ $exists ? 'File found' : 'File not found' }}</div>
                        <div class="text-secondary small">
                            <code>{{ $path }}</code>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-start">
                        <a href="{{ route('admin.system-log.show') }}" class="btn btn-outline-secondary">Refresh</a>
                        <form method="POST" action="{{ route('admin.system-log.destroy') }}" onsubmit="return confirm('Delete the Laravel log file? Laravel will create it again when a new log entry is written.');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger" @disabled(! $exists)>Delete log file</button>
                        </form>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="metric-card">
                            <div class="metric-label">Size</div>
                            <div class="metric-value fs-4">{{ number_format($size) }} bytes</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="metric-card">
                            <div class="metric-label">Last Updated</div>
                            <div class="metric-value fs-6">
                                {{ $updatedAt ? \Carbon\CarbonImmutable::createFromTimestamp($updatedAt)->toDayDateTimeString() : 'Not available' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="metric-card">
                            <div class="metric-label">Access</div>
                            <div class="metric-value fs-6">Admin session only</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning rounded-4">
                    Editing logs is useful for removing noise during debugging, but it also changes diagnostic history. Prefer deleting only after you have captured the error details you need.
                </div>

                <form method="POST" action="{{ route('admin.system-log.update') }}" class="d-grid gap-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="form-label" for="content">Log Content</label>
                        <textarea id="content" name="content" class="form-control font-monospace" rows="28" spellcheck="false">{{ old('content', $content) }}</textarea>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-primary">Save log file</button>
                        <button type="reset" class="btn btn-outline-secondary">Reset unsaved edits</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
