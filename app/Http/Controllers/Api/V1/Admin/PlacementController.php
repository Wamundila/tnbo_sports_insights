<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlacementRequest;
use App\Models\Placement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlacementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Placement::query()->latest('id');

        if ($request->filled('service')) {
            $query->where('service', $request->string('service'));
        }

        if ($request->filled('surface')) {
            $query->where('surface', $request->string('surface'));
        }

        return response()->json([
            'data' => $query->paginate((int) $request->integer('per_page', 15)),
        ]);
    }

    public function store(StorePlacementRequest $request): JsonResponse
    {
        $placement = Placement::query()->create($request->validated());

        return response()->json([
            'data' => $placement,
        ], 201);
    }
}
