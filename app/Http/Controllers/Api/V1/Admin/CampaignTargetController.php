<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCampaignTargetRequest;
use App\Models\CampaignTarget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignTargetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = CampaignTarget::query()->with(['campaign', 'placement'])->latest('id');

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->integer('campaign_id'));
        }

        if ($request->filled('placement_id')) {
            $query->where('placement_id', $request->integer('placement_id'));
        }

        return response()->json([
            'data' => $query->paginate((int) $request->integer('per_page', 15)),
        ]);
    }

    public function store(StoreCampaignTargetRequest $request): JsonResponse
    {
        $target = CampaignTarget::query()->create($request->validated());

        return response()->json([
            'data' => $target->load(['campaign', 'placement']),
        ], 201);
    }
}
