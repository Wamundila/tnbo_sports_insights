<?php

use App\Http\Controllers\Api\V1\Admin\CampaignController;
use App\Http\Controllers\Api\V1\Admin\CampaignCreativeController;
use App\Http\Controllers\Api\V1\Admin\CampaignTargetController;
use App\Http\Controllers\Api\V1\Admin\PlacementController;
use App\Http\Controllers\Api\V1\Admin\SponsorController;
use App\Http\Controllers\Api\V1\Reports\CampaignReportController;
use App\Http\Controllers\Api\V1\Reports\ContentReportController;
use App\Http\Controllers\Api\V1\Reports\LiveReportController;
use App\Http\Controllers\Api\V1\Reports\OverviewReportController;
use App\Http\Controllers\Api\V1\ResolvePlacementsController;
use App\Http\Controllers\Api\V1\StoreEventBatchController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware('insights.auth')
    ->group(function (): void {
        Route::post('/events/batch', StoreEventBatchController::class);
        Route::post('/placements/resolve', ResolvePlacementsController::class);

        Route::prefix('reports')->group(function (): void {
            Route::get('/overview', OverviewReportController::class);
            Route::get('/campaigns/{campaignCode}', CampaignReportController::class);
            Route::get('/content', ContentReportController::class);
            Route::get('/live', LiveReportController::class);
        });

        Route::prefix('admin')->group(function (): void {
            Route::get('/sponsors', [SponsorController::class, 'index']);
            Route::post('/sponsors', [SponsorController::class, 'store']);
            Route::get('/placements', [PlacementController::class, 'index']);
            Route::post('/placements', [PlacementController::class, 'store']);
            Route::get('/campaigns', [CampaignController::class, 'index']);
            Route::post('/campaigns', [CampaignController::class, 'store']);
            Route::get('/creatives', [CampaignCreativeController::class, 'index']);
            Route::post('/creatives', [CampaignCreativeController::class, 'store']);
            Route::get('/targets', [CampaignTargetController::class, 'index']);
            Route::post('/targets', [CampaignTargetController::class, 'store']);
        });
    });
