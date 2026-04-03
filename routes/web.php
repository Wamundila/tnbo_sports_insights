<?php

use App\Http\Controllers\Web\Admin\CampaignController;
use App\Http\Controllers\Web\Admin\CampaignCreativeController;
use App\Http\Controllers\Web\Admin\CampaignTargetController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\GettingStartedController;
use App\Http\Controllers\Web\Admin\PlacementController;
use App\Http\Controllers\Web\Admin\ReportController;
use App\Http\Controllers\Web\Admin\SponsorController;
use App\Http\Controllers\Web\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('admin.login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('admin.login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy'])->name('admin.logout');

    Route::prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/getting-started', [GettingStartedController::class, 'index'])->name('getting-started');

        Route::get('/sponsors', [SponsorController::class, 'index'])->name('sponsors.index');
        Route::post('/sponsors', [SponsorController::class, 'store'])->name('sponsors.store');
        Route::get('/sponsors/{sponsor}/edit', [SponsorController::class, 'edit'])->name('sponsors.edit');
        Route::put('/sponsors/{sponsor}', [SponsorController::class, 'update'])->name('sponsors.update');

        Route::get('/placements', [PlacementController::class, 'index'])->name('placements.index');
        Route::post('/placements', [PlacementController::class, 'store'])->name('placements.store');
        Route::get('/placements/{placement}/edit', [PlacementController::class, 'edit'])->name('placements.edit');
        Route::put('/placements/{placement}', [PlacementController::class, 'update'])->name('placements.update');

        Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
        Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
        Route::get('/campaigns/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
        Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');

        Route::get('/creatives', [CampaignCreativeController::class, 'index'])->name('creatives.index');
        Route::post('/creatives', [CampaignCreativeController::class, 'store'])->name('creatives.store');
        Route::get('/creatives/{creative}/edit', [CampaignCreativeController::class, 'edit'])->name('creatives.edit');
        Route::put('/creatives/{creative}', [CampaignCreativeController::class, 'update'])->name('creatives.update');

        Route::get('/targets', [CampaignTargetController::class, 'index'])->name('targets.index');
        Route::post('/targets', [CampaignTargetController::class, 'store'])->name('targets.store');
        Route::get('/targets/{target}/edit', [CampaignTargetController::class, 'edit'])->name('targets.edit');
        Route::put('/targets/{target}', [CampaignTargetController::class, 'update'])->name('targets.update');

        Route::get('/reports/overview', [ReportController::class, 'overview'])->name('reports.overview');
        Route::get('/reports/campaigns', [ReportController::class, 'campaigns'])->name('reports.campaigns');
        Route::get('/reports/content', [ReportController::class, 'content'])->name('reports.content');
        Route::get('/reports/live', [ReportController::class, 'live'])->name('reports.live');
    });
});
