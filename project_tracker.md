# TNBO Insights Project Tracker

## Current Status

TNBO Insights is now functional as a Laravel-based backend and admin console for sponsor delivery, analytics ingestion, aggregate reporting, and inventory management.

The service currently supports:

- internal event ingestion
- placement resolution for sponsor delivery
- API-key protected service APIs
- session-authenticated admin web access
- sponsor, placement, campaign, creative, and target management
- aggregate rollups and reporting screens

## Completed Work

### 1. Platform Foundation

- Bootstrapped the service as a standalone Laravel application.
- Kept the default Laravel `users` table for admin web access.
- Added API routing and admin web routing.
- Added `X-API-Key` protection for `/api/v1/*` routes.
- Added machine-readable API error codes for integration safety.

### 2. Core Data Model

Implemented schema and models for:

- analytics sessions
- analytics events
- event deduplication
- sponsors
- placements
- campaigns
- campaign creatives
- campaign targets
- campaign delivery logs
- sponsor block events
- daily and hourly aggregate tables

### 3. Analytics Ingestion

- Built `POST /api/v1/events/batch`.
- Added strict payload validation.
- Added event deduplication.
- Added session materialization support.
- Kept client event timestamps as UTC instants while deriving report dates from the configured reporting timezone.

### 4. Sponsor Placement Resolution

- Built `POST /api/v1/placements/resolve`.
- Added target matching by placement, service, surface, campaign status, and campaign date window.
- Added delivery logging for served sponsor responses.
- Added generated `campaign_served` analytics events.
- Generated sponsor-serving analytics events now use the same reporting-date timezone logic as client-ingested events.

### 5. Reporting and Aggregates

- Built daily aggregate rollups for:
  - surface metrics
  - block metrics
  - content metrics
  - campaign metrics
  - user metrics
- Built hourly aggregate rollups for service metrics.
- Added scheduled Artisan commands:
  - `insights:rollup-hourly`
  - `insights:rollup-daily`
- Added report APIs and admin report screens for:
  - overview
  - campaigns
  - content
  - live metrics
- Added `insights:rollup-today` for hourly current-day dashboard visibility.
- Added `insights:repair-event-dates` to repair existing rows after timezone/date derivation fixes.

### 6. Admin Web Console

- Built admin login and logout.
- Built dashboard, reports, and inventory screens using Blade, Bootstrap 5, htmx, and Chart.js.
- Added create and edit flows for:
  - sponsors
  - placements
  - campaigns
  - creatives
  - targets
- Added a Getting Started help page for admins.
- Fixed dashboard chart sizing behavior on the operations screen.

### 7. Creative Management

- Switched creative image and logo fields from URL entry to file upload in the admin UI.
- Added support for storing uploaded creative assets on the public disk.
- Limited creative creation and editing to `image_banner` for now.

### 8. Integration and Documentation

- Added `insights_integration_notes.md` for BFF and service consumers.
- Incorporated integration follow-up changes from BFF.
- Documented API auth, request shapes, response shapes, partial-success behavior, and error codes.
- Documented current Flutter-compatible event ingestion behavior, including `metadata` support.

### 9. Raw Event Retention

- Added config-backed raw event retention controls.
- Added scheduled pruning for old raw analytics events.
- Added pruning for old event dedup rows.
- Added database support for pruning dedup rows efficiently.

### 10. Verification

- Feature and unit test coverage exists for:
  - admin API
  - admin web auth and inventory flows
  - event ingestion
  - placement resolution
  - reporting and rollups
  - raw event retention
- Current local test status at the time of writing:
  - `26` tests passing
  - `152` assertions passing

## Operational Notes

- Aggregate rollups already exist, but they currently run as scheduled Artisan commands, not queued job classes.
- Raw analytics events are now pruned on a retention window rather than kept forever.
- Uploaded creative assets require `php artisan storage:link` in each environment.
- If external clients or devices need to load creative assets, `APP_URL` must be reachable from those clients.
- Placement resolution is currently confirmed working for `home_inline_1` with an active campaign.
- Mobile/BFF should keep sending UTC timestamps; Insights derives `event_date` using `INSIGHTS_REPORTING_TIMEZONE`.

## Work Still To Be Done

### 1. Admin Lifecycle Controls

- Add archive/delete flows for sponsors, placements, campaigns, creatives, and targets.
- Add quick actions in the admin tables:
  - activate
  - pause
  - deactivate

### 2. Placement and Creative Compatibility Enforcement

- Enforce creative-type compatibility between placements and creatives.
- Prevent mismatches in both admin validation and runtime placement resolution.
- Review placement defaults now that creatives are temporarily restricted to `image_banner`.

### 3. Delivery Troubleshooting

- Build an admin troubleshooting tool for “why did this placement not resolve?”
- Show blockers such as:
  - placement inactive
  - target inactive
  - campaign inactive
  - campaign expired
  - no active creative
  - service or surface mismatch
  - creative-type mismatch

### 4. Aggregate Operations and Reliability

- Add rollup status tracking.
- Add admin visibility for last successful hourly and daily rollups.
- Add safe rerun and backfill controls.
- Add repair flows for late-arriving analytics data.
- Add failure monitoring and alerting for rollups.
- Review and tune raw-event retention policy after rollup status visibility is in place.

### 5. Reporting Improvements

- Add report export support, starting with CSV.
- Add richer operational filtering where useful.
- Add clearer empty-state messaging when reports are blank because rollups have not run.

### 6. Admin Access Control

- Add roles and permissions for admin users.
- Separate operator, analyst, and super-admin capabilities if needed.
- Add audit logging for admin changes.

### 7. API and Platform Operations

- Add health and readiness endpoints for infrastructure and BFF.
- Add monitoring around ingestion volume, validation failures, and placement-resolution failures.
- Add campaign-expiry and broken-asset warnings.

### 8. Future UI Enhancements

- Add Livewire only where admin flows become too complex for simple form handling.
- Add DataTables only for larger inventory or reporting grids where pagination/search becomes limiting.

## Recommended Next Slice

The most practical next implementation sequence is:

1. Add activate/pause/delete lifecycle controls.
2. Enforce placement and creative compatibility rules.
3. Build the delivery troubleshooting screen.
4. Add aggregate status, rerun, and backfill operations.

## Done vs Remaining Summary

### Done

- core APIs
- core schema
- admin console
- reporting
- aggregate rollups
- integration notes
- creative uploads

### Remaining

- lifecycle controls
- compatibility enforcement
- troubleshooting tools
- aggregate operations visibility
- admin roles and audit logging
- report export and operational monitoring
