@extends('layouts.admin', [
    'title' => 'Getting Started | TNBO Insights Admin',
    'heading' => 'Getting Started',
    'subheading' => 'A practical guide for admins managing sponsors, inventory, delivery, and reporting.',
    'topbarActions' => '
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary" href="'.route('admin.sponsors.index').'">Add sponsor</a>
            <a class="btn btn-outline-secondary" href="'.route('admin.campaigns.index').'">Open campaigns</a>
        </div>
    ',
])

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="panel-card h-100">
                <div class="section-label mb-2">What This Platform Does</div>
                <h2 class="h4">TNBO Insights is the operations and reporting layer for sponsor delivery.</h2>
                <p class="text-secondary mb-3">
                    Admins use this console to define sponsor inventory, configure campaigns, attach creative assets,
                    set campaign targets, and review performance after delivery starts in the client apps.
                </p>
                <div class="alert alert-warning rounded-4 border-0 mb-0">
                    BFF and client apps send delivery and audience events into Insights. This admin console does not push
                    ads directly to users; it manages the rules and inventory that power those responses.
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="panel-card h-100">
                <div class="section-label mb-2">Quick Links</div>
                <div class="d-grid gap-2">
                    <a class="btn btn-outline-dark text-start" href="{{ route('admin.sponsors.index') }}">Sponsors</a>
                    <a class="btn btn-outline-dark text-start" href="{{ route('admin.placements.index') }}">Placements</a>
                    <a class="btn btn-outline-dark text-start" href="{{ route('admin.campaigns.index') }}">Campaigns</a>
                    <a class="btn btn-outline-dark text-start" href="{{ route('admin.creatives.index') }}">Creatives</a>
                    <a class="btn btn-outline-dark text-start" href="{{ route('admin.targets.index') }}">Targets</a>
                    <a class="btn btn-outline-dark text-start" href="{{ route('admin.reports.overview') }}">Reports</a>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-card mb-4">
        <div class="section-label mb-3">Recommended Workflow</div>
        <div class="row g-3">
            <div class="col-md-6 col-xl-4">
                <div class="metric-card">
                    <div class="metric-label">1. Sponsors</div>
                    <h3 class="h5 mt-2">Create the brand record</h3>
                    <p class="mb-0 text-secondary">Add the sponsor name, code, status, and optional website so campaigns can be linked to the correct advertiser.</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="metric-card">
                    <div class="metric-label">2. Placements</div>
                    <h3 class="h5 mt-2">Define where delivery can happen</h3>
                    <p class="mb-0 text-secondary">Placements describe slots such as <code>home_inline_1</code> or <code>match_center_header_companion</code>.</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="metric-card">
                    <div class="metric-label">3. Campaigns</div>
                    <h3 class="h5 mt-2">Set the delivery rules</h3>
                    <p class="mb-0 text-secondary">Campaigns hold status, priority, targeting JSON, frequency caps, reporting labels, and sponsorship objectives.</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="metric-card">
                    <div class="metric-label">4. Creatives</div>
                    <h3 class="h5 mt-2">Attach the asset payload</h3>
                    <p class="mb-0 text-secondary">Creatives are the ad units a client app can render, including image, text, CTA, destination URL, and metadata.</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="metric-card">
                    <div class="metric-label">5. Targets</div>
                    <h3 class="h5 mt-2">Map campaigns to placements</h3>
                    <p class="mb-0 text-secondary">Targets connect campaigns to specific placements or contexts so placement resolution can return an eligible sponsor block.</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="metric-card">
                    <div class="metric-label">6. Reports</div>
                    <h3 class="h5 mt-2">Review delivery and performance</h3>
                    <p class="mb-0 text-secondary">Use overview, content, campaign, and live reports to monitor attention, reach, CTR, and inventory health.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="panel-card h-100">
                <div class="section-label mb-3">What Each Area Means</div>
                <div class="d-grid gap-3">
                    <div>
                        <h3 class="h5 mb-1">Dashboard</h3>
                        <p class="mb-0 text-secondary">A short operational view of audience activity, sponsor attention, active inventory, and recent trends.</p>
                    </div>
                    <div>
                        <h3 class="h5 mb-1">Reports</h3>
                        <p class="mb-0 text-secondary">Aggregated data for daily/hourly performance. Use this area after campaigns are serving and ingestion is healthy.</p>
                    </div>
                    <div>
                        <h3 class="h5 mb-1">Sponsors</h3>
                        <p class="mb-0 text-secondary">The advertiser directory. One sponsor can own many campaigns.</p>
                    </div>
                    <div>
                        <h3 class="h5 mb-1">Placements</h3>
                        <p class="mb-0 text-secondary">The inventory catalogue. Placements define where sponsored content is allowed to appear in TNBO surfaces.</p>
                    </div>
                    <div>
                        <h3 class="h5 mb-1">Campaigns, Creatives, Targets</h3>
                        <p class="mb-0 text-secondary">Together these define what can serve, how it looks, and where it is eligible to appear.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="panel-card h-100">
                <div class="section-label mb-3">Reporting Terms</div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Term</th>
                            <th>Meaning</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><strong>Served</strong></td>
                            <td>The campaign was selected and returned in a placement resolution response.</td>
                        </tr>
                        <tr>
                            <td><strong>Rendered</strong></td>
                            <td>The client confirms the block was actually shown in the interface.</td>
                        </tr>
                        <tr>
                            <td><strong>Viewed</strong></td>
                            <td>A qualified impression. The sponsor block met the product visibility threshold.</td>
                        </tr>
                        <tr>
                            <td><strong>Clicked</strong></td>
                            <td>The user interacted with the sponsor block and opened or triggered the destination.</td>
                        </tr>
                        <tr>
                            <td><strong>CTR</strong></td>
                            <td>Click-through rate, usually clicks divided by qualified impressions.</td>
                        </tr>
                        <tr>
                            <td><strong>Reach</strong></td>
                            <td>The unique audience count that saw a qualifying sponsor impression.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="panel-card h-100">
                <div class="section-label mb-3">Common Admin Checks</div>
                <div class="d-grid gap-3">
                    <div>
                        <h3 class="h5 mb-1">If a campaign is not serving</h3>
                        <p class="mb-0 text-secondary">Check that the campaign is active, linked to a sponsor, has at least one valid creative, and has targets matching a live placement.</p>
                    </div>
                    <div>
                        <h3 class="h5 mb-1">If reports look empty</h3>
                        <p class="mb-0 text-secondary">Confirm the apps are sending events through BFF, then verify rollups have run for the selected date range.</p>
                    </div>
                    <div>
                        <h3 class="h5 mb-1">If delivery looks too broad</h3>
                        <p class="mb-0 text-secondary">Review targeting JSON, placement assignments, status values, and frequency cap settings on the campaign.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="panel-card h-100">
                <div class="section-label mb-3">First Things To Configure</div>
                <ol class="mb-0 text-secondary">
                    <li class="mb-2">Review the starter placements and disable any slot TNBO will not use.</li>
                    <li class="mb-2">Add each sponsor only once and keep the code stable.</li>
                    <li class="mb-2">Create campaigns with clear reporting labels and activation dates.</li>
                    <li class="mb-2">Keep targeting JSON narrow until delivery is confirmed.</li>
                    <li>Use reports to confirm served, rendered, viewed, and clicked counts are moving together as expected.</li>
                </ol>
            </div>
        </div>
    </div>
@endsection
