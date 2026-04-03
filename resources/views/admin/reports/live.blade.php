@extends('layouts.admin', [
    'title' => 'Live Reports | TNBO Insights Admin',
    'heading' => 'Live Reports',
    'subheading' => 'Hourly monitoring for commentary and live coverage surfaces.',
])

@section('content')
    <div class="panel-card mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3"><label class="form-label">From</label><input type="date" class="form-control" name="date_from" value="{{ $filters['date_from'] }}"></div>
            <div class="col-md-3"><label class="form-label">To</label><input type="date" class="form-control" name="date_to" value="{{ $filters['date_to'] }}"></div>
            <div class="col-md-3">
                <label class="form-label">Service</label>
                <select class="form-select" name="service">
                    <option value="">Media + Match Center</option>
                    <option value="media" @selected(($filters['service'] ?? '') === 'media')>media</option>
                    <option value="match_center" @selected(($filters['service'] ?? '') === 'match_center')>match_center</option>
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">Match ID</label><input class="form-control" name="match_id" value="{{ $filters['match_id'] ?? '' }}" placeholder="match_5541"></div>
            <div class="col-12"><button class="btn btn-primary">Refresh live report</button></div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3"><div class="metric-card"><div class="metric-label">Audio Starts</div><div class="metric-value">{{ number_format($report['summary']['audio_starts']) }}</div></div></div>
        <div class="col-md-6 col-xl-3"><div class="metric-card"><div class="metric-label">Listen Seconds</div><div class="metric-value">{{ number_format($report['summary']['listen_seconds_total']) }}</div></div></div>
        <div class="col-md-6 col-xl-3"><div class="metric-card"><div class="metric-label">Sponsor Impressions</div><div class="metric-value">{{ number_format($report['summary']['sponsor_impressions']) }}</div></div></div>
        <div class="col-md-6 col-xl-3"><div class="metric-card"><div class="metric-label">Sponsor Clicks</div><div class="metric-value">{{ number_format($report['summary']['sponsor_clicks']) }}</div></div></div>
    </div>

    <div class="panel-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Hour</th><th>Service</th><th>Match</th><th class="text-end">Listeners</th><th class="text-end">Listen Seconds</th></tr></thead>
                <tbody>
                @forelse ($report['items'] as $item)
                    <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($item->metric_hour)->format('Y-m-d H:i') }}</td>
                        <td>{{ $item->service }}</td>
                        <td>{{ $item->match_id ?: 'n/a' }}</td>
                        <td class="text-end">{{ number_format($item->unique_users) }}</td>
                        <td class="text-end">{{ number_format($item->audio_listen_seconds) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">No live aggregates in range.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
