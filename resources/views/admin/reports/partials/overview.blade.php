<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3"><div class="metric-card"><div class="metric-label">Screen Views</div><div class="metric-value">{{ number_format($report['summary']['screen_views']) }}</div></div></div>
    <div class="col-md-6 col-xl-3"><div class="metric-card"><div class="metric-label">Sessions</div><div class="metric-value">{{ number_format($report['summary']['sessions']) }}</div></div></div>
    <div class="col-md-6 col-xl-3"><div class="metric-card"><div class="metric-label">Users</div><div class="metric-value">{{ number_format($report['summary']['unique_users']) }}</div></div></div>
    <div class="col-md-6 col-xl-3"><div class="metric-card"><div class="metric-label">Sponsor Clicks</div><div class="metric-value">{{ number_format($report['summary']['sponsor_clicks']) }}</div></div></div>
</div>

<div class="row g-4">
    <div class="col-xl-6">
        <div class="panel-card">
            <div class="section-label mb-2">Top Surfaces</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Service</th><th>Surface</th><th class="text-end">Views</th><th class="text-end">Users</th></tr></thead>
                    <tbody>
                    @forelse ($report['top_surfaces'] as $surface)
                        <tr>
                            <td>{{ $surface->service }}</td>
                            <td>{{ $surface->surface }}</td>
                            <td class="text-end">{{ number_format($surface->screen_views) }}</td>
                            <td class="text-end">{{ number_format($surface->unique_users) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-4">No data in range.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="alert alert-light border rounded-4 mt-3 mb-0 small text-secondary">
                <strong>How to read this:</strong> Views count screen activity. Users count distinct people for each
                surface in the selected dates. The main Users card is the distinct total across the selected report
                filters, so it is not a sum of these rows.
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="panel-card">
            <div class="section-label mb-2">Top Campaigns</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Campaign</th><th class="text-end">Impressions</th><th class="text-end">Clicks</th></tr></thead>
                    <tbody>
                    @forelse ($report['top_campaigns'] as $campaign)
                        <tr>
                            <td>{{ $campaign->campaign_id ?: 'Unknown' }}</td>
                            <td class="text-end">{{ number_format($campaign->qualified_impressions) }}</td>
                            <td class="text-end">{{ number_format($campaign->clicks) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-secondary py-4">No campaign data in range.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
