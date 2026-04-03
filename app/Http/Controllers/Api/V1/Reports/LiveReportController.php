<?php

namespace App\Http\Controllers\Api\V1\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\LiveReportRequest;
use App\Services\Reporting\ReportService;
use Illuminate\Http\JsonResponse;

class LiveReportController extends Controller
{
    public function __invoke(LiveReportRequest $request, ReportService $reportService): JsonResponse
    {
        return response()->json($reportService->live($request->validated()));
    }
}
