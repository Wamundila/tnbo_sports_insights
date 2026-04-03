<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\AggDailyCampaignMetric;
use App\Models\AggDailySurfaceMetric;
use App\Models\Campaign;
use App\Models\Placement;
use App\Models\Sponsor;
use App\Services\Reporting\ReportService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, ReportService $reportService): View
    {
        $to = $request->filled('date_to')
            ? CarbonImmutable::parse($request->string('date_to'))
            : CarbonImmutable::today();
        $from = $request->filled('date_from')
            ? CarbonImmutable::parse($request->string('date_from'))
            : $to->subDays(6);

        $overview = $reportService->overview([
            'date_from' => $from->toDateString(),
            'date_to' => $to->toDateString(),
        ]);

        $surfaceTrend = AggDailySurfaceMetric::query()
            ->whereDate('metric_date', '>=', $from->toDateString())
            ->whereDate('metric_date', '<=', $to->toDateString())
            ->selectRaw('DATE(metric_date) as metric_date')
            ->selectRaw('SUM(screen_views) as screen_views')
            ->selectRaw('SUM(sponsor_impressions) as sponsor_impressions')
            ->groupBy('metric_date')
            ->orderBy('metric_date')
            ->get();

        $campaignTrend = AggDailyCampaignMetric::query()
            ->whereDate('metric_date', '>=', $from->toDateString())
            ->whereDate('metric_date', '<=', $to->toDateString())
            ->selectRaw('DATE(metric_date) as metric_date')
            ->selectRaw('SUM(clicks) as clicks')
            ->selectRaw('SUM(qualified_impressions) as impressions')
            ->groupBy('metric_date')
            ->orderBy('metric_date')
            ->get();

        return view('admin.dashboard', [
            'dateFrom' => $from->toDateString(),
            'dateTo' => $to->toDateString(),
            'overview' => $overview,
            'surfaceTrend' => $surfaceTrend,
            'campaignTrend' => $campaignTrend,
            'inventoryStats' => [
                'sponsors' => Sponsor::query()->count(),
                'campaigns' => Campaign::query()->count(),
                'active_campaigns' => Campaign::query()->where('status', 'active')->count(),
                'placements' => Placement::query()->where('is_active', true)->count(),
            ],
        ]);
    }
}
