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
                <div class="metric-label">Screen Views</div>
                <div class="metric-value">{{ number_format($overview['summary']['screen_views']) }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-label">Unique Users</div>
                <div class="metric-value">{{ number_format($overview['summary']['unique_users']) }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-label">Sponsor Impressions</div>
                <div class="metric-value">{{ number_format($overview['summary']['sponsor_impressions']) }}</div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-label">Sponsor Clicks</div>
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
                </div>
                <div class="dashboard-chart-frame">
                    <canvas id="surfaceTrendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="panel-card h-100">
                <div class="section-label mb-2">Inventory</div>
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
                <div class="section-label mb-2">Active Users</div>
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
                <div class="section-label mb-2">Top Surfaces</div>
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
            </div>
        </div>
        <div class="col-xl-6">
            <div class="panel-card">
                <div class="section-label mb-2">Top Campaigns</div>
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
