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
            <div class="col-md-6 col-xl-2">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="metric-label">Served</div>
                        <x-admin.info-modal id="campaign-served-info" title="Served">
                            <p class="mb-0">Served means Insights selected the campaign and returned it from placement resolution. It does not prove the user saw it.</p>
                        </x-admin.info-modal>
                    </div>
                    <div class="metric-value">{{ number_format($report['summary']['served_count']) }}</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-2">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="metric-label">Rendered</div>
                        <x-admin.info-modal id="campaign-rendered-info" title="Rendered">
                            <p class="mb-0">Rendered means the app reported that the sponsor block was placed on screen. It updates when render events are ingested and rollups refresh.</p>
                        </x-admin.info-modal>
                    </div>
                    <div class="metric-value">{{ number_format($report['summary']['rendered_count']) }}</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="metric-label">Qualified Impressions</div>
                        <x-admin.info-modal id="campaign-impressions-info" title="Qualified Impressions">
                            <p class="mb-0">Qualified impressions are sponsor views reported by the app, such as `sponsor_impression` or `sponsor_block_view` events.</p>
                        </x-admin.info-modal>
                    </div>
                    <div class="metric-value">{{ number_format($report['summary']['qualified_impressions']) }}</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-2">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="metric-label">Clicks</div>
                        <x-admin.info-modal id="campaign-clicks-info" title="Clicks">
                            <p class="mb-0">Clicks count sponsor click and CTA click events for this campaign.</p>
                        </x-admin.info-modal>
                    </div>
                    <div class="metric-value">{{ number_format($report['summary']['clicks']) }}</div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="metric-label">Unique Reach</div>
                        <x-admin.info-modal id="campaign-reach-info" title="Unique Reach">
                            <p class="mb-0">Unique reach is the distinct number of signed-in or anonymous users with campaign activity in the selected date range. It is recalculated from raw events to avoid double-counting.</p>
                        </x-admin.info-modal>
                    </div>
                    <div class="metric-value">{{ number_format($report['summary']['unique_users_reached']) }}</div>
                </div>
            </div>
        </div>

        <div class="alert alert-light border rounded-4 mb-4 small text-secondary">
            <strong>Update timing:</strong> Today’s campaign values refresh hourly. Completed days are finalized by the daily rollup, normally after 01:00.
        </div>

        <div class="row g-4">
            <div class="col-xl-6">
                <div class="panel-card">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div class="section-label mb-0">By Placement</div>
                        <x-admin.info-modal id="campaign-by-placement-info" title="By Placement">
                            <p class="mb-0">This breaks the campaign down by ad slot. Use it to see which placement is producing impressions, clicks, and CTR.</p>
                        </x-admin.info-modal>
                    </div>
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
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div class="section-label mb-0">By Date</div>
                        <x-admin.info-modal id="campaign-by-date-info" title="By Date">
                            <p class="mb-0">This shows daily campaign delivery. Current-day rows can change during the day; older rows settle after the daily rollup.</p>
                        </x-admin.info-modal>
                    </div>
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
