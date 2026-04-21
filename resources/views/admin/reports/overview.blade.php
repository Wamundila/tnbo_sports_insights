@extends('layouts.admin', [
    'title' => 'Overview Reports | TNBO Insights Admin',
    'heading' => 'Overview Reports',
    'subheading' => 'High-level audience and sponsor reporting across the selected date range.',
])

@section('content')
    <div class="panel-card mb-4">
        <form
            method="GET"
            action="{{ route('admin.reports.overview') }}"
            hx-get="{{ route('admin.reports.overview') }}"
            hx-target="#overview-report"
            hx-push-url="true"
            class="row g-3 align-items-end"
        >
            <div class="col-md-3">
                <label class="form-label">From</label>
                <input type="date" class="form-control" name="date_from" value="{{ $filters['date_from'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To</label>
                <input type="date" class="form-control" name="date_to" value="{{ $filters['date_to'] }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Service</label>
                <select class="form-select" name="service">
                    <option value="">All services</option>
                    @foreach ($allowedServices as $service)
                        <option value="{{ $service }}" @selected(($filters['service'] ?? '') === $service)>{{ $service }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Surface</label>
                <input class="form-control" name="surface" value="{{ $filters['surface'] ?? '' }}" placeholder="home_page">
            </div>
            <div class="col-12">
                <button class="btn btn-primary">Refresh report</button>
            </div>
        </form>
    </div>

    <div id="overview-report">
        @include('admin.reports.partials.overview', ['report' => $report])
    </div>
@endsection
