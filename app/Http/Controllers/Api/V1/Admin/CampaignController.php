<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCampaignRequest;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Campaign::query()->with('sponsor')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('sponsor_id')) {
            $query->where('sponsor_id', $request->integer('sponsor_id'));
        }

        return response()->json([
            'data' => $query->paginate((int) $request->integer('per_page', 15)),
        ]);
    }

    public function store(StoreCampaignRequest $request): JsonResponse
    {
        $campaign = Campaign::query()->create($request->validated());

        return response()->json([
            'data' => $campaign->load('sponsor'),
        ], 201);
    }
}
