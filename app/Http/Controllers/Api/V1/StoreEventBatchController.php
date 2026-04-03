<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventBatchRequest;
use App\Services\Analytics\EventIngestionService;
use Illuminate\Http\JsonResponse;

class StoreEventBatchController extends Controller
{
    public function __invoke(StoreEventBatchRequest $request, EventIngestionService $eventIngestionService): JsonResponse
    {
        $result = $eventIngestionService->ingestBatch($request->validated());

        return response()->json($result);
    }
}
