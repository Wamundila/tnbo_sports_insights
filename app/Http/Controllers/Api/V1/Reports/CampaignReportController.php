<?php

namespace App\Http\Controllers\Api\V1\Reports;

use App\Http\Controllers\Controller;
use App\Services\Reporting\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignReportController extends Controller
{
    public function __invoke(string $campaignCode, Request $request, ReportService $reportService): JsonResponse
    {
        return response()->json($reportService->campaign($campaignCode, $request->query()));
    }
}
