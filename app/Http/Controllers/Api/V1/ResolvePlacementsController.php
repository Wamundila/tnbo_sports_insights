<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResolvePlacementsRequest;
use App\Services\Sponsors\PlacementResolutionService;
use Illuminate\Http\JsonResponse;

class ResolvePlacementsController extends Controller
{
    public function __invoke(
        ResolvePlacementsRequest $request,
        PlacementResolutionService $placementResolutionService
    ): JsonResponse {
        return response()->json([
            'placements' => $placementResolutionService->resolve($request->validated()),
        ]);
    }
}
