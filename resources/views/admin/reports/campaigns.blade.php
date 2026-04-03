@extends('layouts.admin', [
    'title' => 'Campaign Reports | TNBO Insights Admin',
    'heading' => 'Campaign Reports',
    'subheading' => 'Commercial reporting by campaign, placement, and date range.',
])

@section('content')
    <div class="panel-card mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Campaign</label>
                <select class="form-select" name="campaign_id">
                    <option value="">Select a campaign</option>
                    @foreach ($campaigns as $campaign)
                        <option value="{{ $campaign->code }}" @selected($selectedCampaign === $campaign->code)>{{ $campaign->name }} ({{ $campaign->code }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">From</label><input type="date" class="form-control" name="date_from" value="{{ $filters['date_from'] }}"></div>
            <div class="col-md-3"><label class="form-label">To</label><input type="date" class="form-control" name="date_to" value="{{ $filters['date_to'] }}"></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Load report</button></div>
        </form>
    </div>

    @if (! $report)
        <div class="panel-card text-secondary">Choose a campaign to view its report.</div>
    @else
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-2"><div class="metric-card"><div class="metric-label">Served</div><div class="metric-value">{{ number_format($report['summary']['served_count']) }}</div></div></div>
            <div class="col-md-6 col-xl-2"><div class="metric-card"><div class="metric-label">Rendered</div><div class="metric-value">{{ number_format($report['summary']['rendered_count']) }}</div></div></div>
            <div class="col-md-6 col-xl-3"><div class="metric-card"><div class="metric-label">Qualified Impressions</div><div class="metric-value">{{ number_format($report['summary']['qualified_impressions']) }}</div></div></div>
            <div class="col-md-6 col-xl-2"><div class="metric-card"><div class="metric-label">Clicks</div><div class="metric-value">{{ number_format($report['summary']['clicks']) }}</div></div></div>
            <div class="col-md-6 col-xl-3"><div class="metric-card"><div class="metric-label">Unique Reach</div><div class="metric-value">{{ number_format($report['summary']['unique_users_reached']) }}</div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-xl-6">
                <div class="panel-card">
                    <div class="section-label mb-2">By Placement</div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Placement</th><th class="text-end">Impressions</th><th class="text-end">Clicks</th><th class="text-end">CTR</th></tr></thead>
                            <tbody>
                            @foreach ($report['by_placement'] as $row)
                                <tr>
                                    <td>{{ $row['placement_id'] }}</td>
                                    <td class="text-end">{{ number_format($row['qualified_impressions']) }}</td>
                                    <td class="text-end">{{ number_format($row['clicks']) }}</td>
                                    <td class="text-end">{{ number_format($row['ctr'] * 100, 2) }}%</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="panel-card">
                    <div class="section-label mb-2">By Date</div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Date</th><th class="text-end">Served</th><th class="text-end">Impressions</th><th class="text-end">Clicks</th></tr></thead>
                            <tbody>
                            @foreach ($report['by_date'] as $row)
                                <tr>
                                    <td>{{ \Illuminate\Support\Carbon::parse($row->metric_date)->toDateString() }}</td>
                                    <td class="text-end">{{ number_format($row->served_count) }}</td>
                                    <td class="text-end">{{ number_format($row->qualified_impressions) }}</td>
                                    <td class="text-end">{{ number_format($row->clicks) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
