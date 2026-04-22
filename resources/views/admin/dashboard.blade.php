@extends('layouts.admin', [
    'title' => 'Dashboard | TNBO Insights Admin',
    'heading' => 'Operational Dashboard',
    'subheading' => 'Seven-day view across audience growth, sponsor delivery, and inventory readiness.',
])

@push('styles')
    <style>
        .dashboard-chart-frame {
            position: relative;
            height: 320px;
            min-height: 320px;
        }

        .dashboard-chart-frame canvas {
            width: 100% !important;
            height: 100% !important;
        }

        @media (max-width: 991.98px) {
            .dashboard-chart-frame {
                height: 280px;
                min-height: 280px;
            }
        }
    </style>
@endpush

@section('content')
    <form method="GET" action="{{ route('admin.dashboard') }}" class="panel-card mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">From</label>
                <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">To</label>
                <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100">Refresh dashboard</button>
            </div>
        </div>
    </form>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="metric-label">Screen Views</div>
                    <x-admin.info-modal id="dashboard-screen-views-info" title="Screen Views">
                        <p class="mb-0">Screen views count how many times app screens were opened in the selected date range. This is activity volume, not unique people.</p>
                    </x-admin.info-modal>
                </div>
                <div class="metric-value">{{ number_format($overview['summary']['screen_views']) }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="metric-label">Unique Users</div>
                    <x-admin.info-modal id="dashboard-unique-users-info" title="Unique Users">
                        <p class="mb-0">Unique users count distinct signed-in users or anonymous users in the selected date range. The value updates after raw events arrive and dashboard aggregates refresh hourly.</p>
                    </x-admin.info-modal>
                </div>
                <div class="metric-value">{{ number_format($overview['summary']['unique_users']) }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="metric-label">Sponsor Impressions</div>
                    <x-admin.info-modal id="dashboard-sponsor-impressions-info" title="Sponsor Impressions">
                        <p class="mb-0">Sponsor impressions count sponsor blocks that the app reported as viewed. Served ads do not become impressions until the app sends a view or impression event.</p>
                    </x-admin.info-modal>
                </div>
                <div class="metric-value">{{ number_format($overview['summary']['sponsor_impressions']) }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="metric-label">Sponsor Clicks</div>
                    <x-admin.info-modal id="dashboard-sponsor-clicks-info" title="Sponsor Clicks">
                        <p class="mb-0">Sponsor clicks count user interactions with sponsor blocks or CTA buttons. These update after click events are received and aggregates refresh.</p>
                    </x-admin.info-modal>
                </div>
                <div class="metric-value">{{ number_format($overview['summary']['sponsor_clicks']) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="panel-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="section-label">Trend</div>
                        <h2 class="h4 mb-0">Audience vs Sponsor Attention</h2>
                    </div>
                    <x-admin.info-modal id="dashboard-trend-info" title="Audience vs Sponsor Attention">
                        <p class="mb-0">This chart compares screen views with sponsor impressions by day. It helps show whether sponsor attention is moving with audience activity. Current-day values refresh hourly; completed days are finalized by the daily rollup.</p>
                    </x-admin.info-modal>
                </div>
                <div class="dashboard-chart-frame">
                    <canvas id="surfaceTrendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="panel-card h-100">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div class="section-label mb-0">Inventory</div>
                    <x-admin.info-modal id="dashboard-inventory-info" title="Inventory">
                        <p class="mb-0">Sponsors are advertiser accounts. Placements are app slots where ads can appear. Campaigns are commercial campaigns. Active campaigns are campaigns currently marked active; date windows and targets still affect whether they can serve.</p>
                    </x-admin.info-modal>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-label">Sponsors</div>
                            <div class="metric-value">{{ number_format($inventoryStats['sponsors']) }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-label">Placements</div>
                            <div class="metric-value">{{ number_format($inventoryStats['placements']) }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-label">Campaigns</div>
                            <div class="metric-value">{{ number_format($inventoryStats['campaigns']) }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metric-card">
                            <div class="metric-label">Active Campaigns</div>
                            <div class="metric-value">{{ number_format($inventoryStats['active_campaigns']) }}</div>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div class="section-label mb-0">Active Users</div>
                    <x-admin.info-modal id="dashboard-active-users-info" title="Active Users">
                        <p class="mb-2">DAU is distinct users on the selected end date. WAU is distinct users in the last 7 days ending on that date. MAU is distinct users in the last 30 days ending on that date.</p>
                        <p class="mb-0">These are calculated from raw events, so they can update as soon as events are ingested.</p>
                    </x-admin.info-modal>
                </div>
                <div class="d-grid gap-2">
                    <div class="d-flex justify-content-between"><span>DAU</span><strong>{{ number_format($overview['active_users']['dau']) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>WAU</span><strong>{{ number_format($overview['active_users']['wau']) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>MAU</span><strong>{{ number_format($overview['active_users']['mau']) }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="panel-card">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div class="section-label mb-0">Top Surfaces</div>
                    <x-admin.info-modal id="dashboard-top-surfaces-info" title="Top Surfaces">
                        <p class="mb-2">Top surfaces are ranked by screen views. Users count distinct people for each surface in the selected dates.</p>
                        <p class="mb-0">Current-day values refresh hourly. Completed days are finalized by the daily rollup, normally after 01:00.</p>
                    </x-admin.info-modal>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Service</th>
                            <th>Surface</th>
                            <th class="text-end">Views</th>
                            <th class="text-end">Users</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($overview['top_surfaces'] as $surface)
                            <tr>
                                <td>{{ $surface->service }}</td>
                                <td>{{ $surface->surface }}</td>
                                <td class="text-end">{{ number_format($surface->screen_views) }}</td>
                                <td class="text-end">{{ number_format($surface->unique_users) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-secondary py-4">No aggregate data yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-light border rounded-4 mt-3 mb-0 small text-secondary">
                    <strong>How to read this:</strong> Views count screen activity. Users count distinct people for that
                    surface in the selected dates. DAU, WAU and MAU are separate active-user totals across the whole app
                    window, so they will not always match a single surface row. Today refreshes hourly; completed days are
                    finalized by the daily rollup.
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="panel-card">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div class="section-label mb-0">Top Campaigns</div>
                    <x-admin.info-modal id="dashboard-top-campaigns-info" title="Top Campaigns">
                        <p class="mb-2">Impressions are sponsor views reported by the app. Clicks are sponsor or CTA clicks. Reach is distinct users for that campaign in the selected dates.</p>
                        <p class="mb-0">These numbers refresh hourly for today and are finalized by the daily rollup for completed days.</p>
                    </x-admin.info-modal>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Campaign</th>
                            <th class="text-end">Impressions</th>
                            <th class="text-end">Clicks</th>
                            <th class="text-end">Reach</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($overview['top_campaigns'] as $campaign)
                            <tr>
                                <td>{{ $campaign->campaign_id ?: 'Unknown' }}</td>
                                <td class="text-end">{{ number_format($campaign->qualified_impressions) }}</td>
                                <td class="text-end">{{ number_format($campaign->clicks) }}</td>
                                <td class="text-end">{{ number_format($campaign->unique_reach) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-secondary py-4">No campaign aggregates yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-light border rounded-4 mt-3 mb-0 small text-secondary">
                    <strong>How to read this:</strong> Impressions are sponsor views. Clicks are sponsor or CTA clicks.
                    Reach is distinct users for the campaign. Today refreshes hourly; completed days are finalized by the
                    daily rollup.
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const trendLabels = @json($surfaceTrend->pluck('metric_date'));
        const screenViews = @json($surfaceTrend->pluck('screen_views'));
        const sponsorImpressions = @json($surfaceTrend->pluck('sponsor_impressions'));
        const surfaceTrendCanvas = document.getElementById('surfaceTrendChart');

        if (surfaceTrendCanvas) {
            window.surfaceTrendChart?.destroy();

            window.surfaceTrendChart = new Chart(surfaceTrendCanvas, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [
                        {
                            label: 'Screen Views',
                            data: screenViews,
                            borderColor: '#20384a',
                            backgroundColor: 'rgba(32, 56, 74, 0.12)',
                            tension: 0.35,
                            fill: true
                        },
                        {
                            label: 'Sponsor Impressions',
                            data: sponsorImpressions,
                            borderColor: '#c25b2d',
                            backgroundColor: 'rgba(194, 91, 45, 0.10)',
                            tension: 0.35,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    resizeDelay: 150,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
@endpush
