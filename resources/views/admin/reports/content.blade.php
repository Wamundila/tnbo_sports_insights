@extends('layouts.admin', [
    'title' => 'Content Reports | TNBO Insights Admin',
    'heading' => 'Content Reports',
    'subheading' => 'Editorial and media performance from the daily content rollups.',
])

@section('content')
    <div class="panel-card mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3"><label class="form-label">From</label><input type="date" class="form-control" name="date_from" value="{{ $filters['date_from'] }}"></div>
            <div class="col-md-3"><label class="form-label">To</label><input type="date" class="form-control" name="date_to" value="{{ $filters['date_to'] }}"></div>
            <div class="col-md-3">
                <label class="form-label">Service</label>
                <select class="form-select" name="service">
                    <option value="">All services</option>
                    @foreach ($allowedServices as $service)
                        <option value="{{ $service }}" @selected(($filters['service'] ?? '') === $service)>{{ $service }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">Content Type</label><input class="form-control" name="content_type" value="{{ $filters['content_type'] ?? '' }}" placeholder="article"></div>
            <div class="col-12"><button class="btn btn-primary">Refresh content report</button></div>
        </form>
    </div>

    <div class="panel-card">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
            <div class="section-label mb-0">Content Performance</div>
            <x-admin.info-modal id="content-performance-info" title="Content Performance">
                <p class="mb-2">Opens count content-open events. Users count distinct users for each content item. Completions count completion events such as article or video completes.</p>
                <p class="mb-0">Content reports use daily rollups. Today’s values refresh hourly; completed days are finalized by the daily rollup.</p>
            </x-admin.info-modal>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Content</th><th>Service</th><th class="text-end">Opens</th><th class="text-end">Users</th><th class="text-end">Completions</th></tr></thead>
                <tbody>
                @forelse ($report['items'] as $item)
                    <tr>
                        <td>
                            <div><code>{{ $item->content_id }}</code></div>
                            <div class="text-secondary small">{{ $item->content_type }}</div>
                        </td>
                        <td>{{ $item->service }}</td>
                        <td class="text-end">{{ number_format($item->opens) }}</td>
                        <td class="text-end">{{ number_format($item->unique_users) }}</td>
                        <td class="text-end">{{ number_format($item->completions) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">No content aggregates in range.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
