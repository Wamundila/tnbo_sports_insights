<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="metric-label">Screen Views</div>
                <x-admin.info-modal id="overview-screen-views-info" title="Screen Views">
                    <p class="mb-0">Screen views count screen-open activity in the selected report range. This can be higher than users because one user can open many screens.</p>
                </x-admin.info-modal>
            </div>
            <div class="metric-value">{{ number_format($report['summary']['screen_views']) }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="metric-label">Sessions</div>
                <x-admin.info-modal id="overview-sessions-info" title="Sessions">
                    <p class="mb-0">Sessions count unique app sessions seen in the selected range. Session totals come from daily rollups and refresh hourly for the current day.</p>
                </x-admin.info-modal>
            </div>
            <div class="metric-value">{{ number_format($report['summary']['sessions']) }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="metric-label">Users</div>
                <x-admin.info-modal id="overview-users-info" title="Users">
                    <p class="mb-0">Users count distinct signed-in or anonymous people in the selected filters. This is not a sum of the surface rows below.</p>
                </x-admin.info-modal>
            </div>
            <div class="metric-value">{{ number_format($report['summary']['unique_users']) }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="metric-label">Sponsor Clicks</div>
                <x-admin.info-modal id="overview-sponsor-clicks-info" title="Sponsor Clicks">
                    <p class="mb-0">Sponsor clicks count sponsor or CTA click events. They update after the app sends click events and aggregates refresh.</p>
                </x-admin.info-modal>
            </div>
            <div class="metric-value">{{ number_format($report['summary']['sponsor_clicks']) }}</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-6">
        <div class="panel-card">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <div class="section-label mb-0">Top Surfaces</div>
                <x-admin.info-modal id="overview-top-surfaces-info" title="Top Surfaces">
                    <p class="mb-2">Top surfaces are ranked by views. Users are distinct people per surface for the selected report range.</p>
                    <p class="mb-0">Today refreshes hourly. Completed days are finalized by the daily rollup, normally after 01:00.</p>
                </x-admin.info-modal>
            </div>
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
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <div class="section-label mb-0">Top Campaigns</div>
                <x-admin.info-modal id="overview-top-campaigns-info" title="Top Campaigns">
                    <p class="mb-2">Impressions are sponsor views. Clicks are sponsor or CTA clicks. Campaign reach is shown on the dashboard and campaign detail reports as distinct users.</p>
                    <p class="mb-0">Current-day campaign values refresh hourly after sponsor events are ingested.</p>
                </x-admin.info-modal>
            </div>
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
            <div class="alert alert-light border rounded-4 mt-3 mb-0 small text-secondary">
                <strong>How to read this:</strong> Impressions are sponsor views. Clicks are sponsor or CTA clicks.
                Current-day campaign values refresh hourly; completed days are finalized by the daily rollup.
            </div>
        </div>
    </div>
</div>
