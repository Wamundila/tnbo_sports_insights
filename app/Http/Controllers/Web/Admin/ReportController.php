<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Services\Reporting\ReportService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function overview(Request $request, ReportService $reportService): View
    {
        [$filters, $report] = $this->buildReportPayload($request, $reportService, 'overview');
        $view = $request->header('HX-Request') === 'true'
            ? 'admin.reports.partials.overview'
            : 'admin.reports.overview';

        return view($view, [
            'filters' => $filters,
            'report' => $report,
            'allowedServices' => config('insights.allowed_services'),
        ]);
    }

    public function campaigns(Request $request, ReportService $reportService): View
    {
        $filters = $this->normalizeFilters($request);
        $campaigns = Campaign::query()->with('sponsor')->orderBy('name')->get();
        $selectedCampaign = $request->string('campaign_id')->toString();

        $report = $selectedCampaign !== ''
            ? $reportService->campaign($selectedCampaign, $filters)
            : null;

        return view('admin.reports.campaigns', [
            'filters' => $filters,
            'campaigns' => $campaigns,
            'selectedCampaign' => $selectedCampaign,
            'report' => $report,
        ]);
    }

    public function content(Request $request, ReportService $reportService): View
    {
        [$filters, $report] = $this->buildReportPayload($request, $reportService, 'content');

        return view('admin.reports.content', [
            'filters' => $filters,
            'report' => $report,
            'allowedServices' => config('insights.allowed_services'),
        ]);
    }

    public function live(Request $request, ReportService $reportService): View
    {
        [$filters, $report] = $this->buildReportPayload($request, $reportService, 'live');

        return view('admin.reports.live', [
            'filters' => $filters,
            'report' => $report,
        ]);
    }

    private function buildReportPayload(Request $request, ReportService $reportService, string $method): array
    {
        $filters = $this->normalizeFilters($request);

        return [$filters, $reportService->{$method}($filters)];
    }

    private function normalizeFilters(Request $request): array
    {
        $to = $request->filled('date_to')
            ? CarbonImmutable::parse($request->string('date_to'))
            : CarbonImmutable::today();
        $from = $request->filled('date_from')
            ? CarbonImmutable::parse($request->string('date_from'))
            : $to->subDays(6);

        return array_filter([
            'date_from' => $from->toDateString(),
            'date_to' => $to->toDateString(),
            'service' => $request->string('service')->toString() ?: null,
            'surface' => $request->string('surface')->toString() ?: null,
            'campaign_id' => $request->string('campaign_id')->toString() ?: null,
            'content_type' => $request->string('content_type')->toString() ?: null,
            'match_id' => $request->string('match_id')->toString() ?: null,
            'limit' => $request->integer('limit', 25),
        ], fn ($value) => $value !== null && $value !== '');
    }
}
