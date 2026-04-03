<?php

namespace App\Http\Controllers\Api\V1\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\ContentReportRequest;
use App\Services\Reporting\ReportService;
use Illuminate\Http\JsonResponse;

class ContentReportController extends Controller
{
    public function __invoke(ContentReportRequest $request, ReportService $reportService): JsonResponse
    {
        return response()->json($reportService->content($request->validated()));
    }
}
