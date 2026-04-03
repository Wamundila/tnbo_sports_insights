<?php

namespace App\Http\Controllers\Api\V1\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\OverviewReportRequest;
use App\Services\Reporting\ReportService;
use Illuminate\Http\JsonResponse;

class OverviewReportController extends Controller
{
    public function __invoke(OverviewReportRequest $request, ReportService $reportService): JsonResponse
    {
        return response()->json($reportService->overview($request->validated()));
    }
}
