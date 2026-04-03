<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSponsorRequest;
use App\Models\Sponsor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Sponsor::query()->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return response()->json([
            'data' => $query->paginate((int) $request->integer('per_page', 15)),
        ]);
    }

    public function store(StoreSponsorRequest $request): JsonResponse
    {
        $sponsor = Sponsor::query()->create($request->validated());

        return response()->json([
            'data' => $sponsor,
        ], 201);
    }
}
