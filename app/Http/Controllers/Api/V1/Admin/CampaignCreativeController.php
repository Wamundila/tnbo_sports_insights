<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCampaignCreativeRequest;
use App\Models\CampaignCreative;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignCreativeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = CampaignCreative::query()->with('campaign')->latest('id');

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->integer('campaign_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return response()->json([
            'data' => $query->paginate((int) $request->integer('per_page', 15)),
        ]);
    }

    public function store(StoreCampaignCreativeRequest $request): JsonResponse
    {
        $creative = CampaignCreative::query()->create($request->validated());

        return response()->json([
            'data' => $creative->load('campaign'),
        ], 201);
    }
}
