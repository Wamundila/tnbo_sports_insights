# TNBO Insights Project Tasks

## Current Priority

### 1. Raw Event Retention and Table Growth

Status: `completed`

Implemented:

- config-backed raw event retention window
- scheduled prune command for old analytics events
- pruning of old dedup rows
- regression test coverage for retention cutoff behavior

Follow-up still pending:

- admin visibility for prune status
- rollup watermarks before aggressive retention changes
- longer-term table partitioning strategy for `analytics_events`

## Recently Completed

### Platform and API

Status: `completed`

- core analytics ingestion API
- placement resolution API
- report APIs
- `X-API-Key` protection for `/api/v1/*`
- machine-readable API error codes

### Admin Console

Status: `completed`

- Laravel Blade admin login and dashboard
- sponsor, placement, campaign, creative, and target management
- edit flows for inventory records
- getting started page for admins
- dashboard chart sizing fix

### Creative Management

Status: `completed`

- image and logo upload support
- `image_banner` only restriction for creative type

### Reporting and Rollups

Status: `completed`

- daily rollups
- hourly rollups
- hourly current-day daily aggregate refresh for dashboard visibility
- scheduled rollup commands
- admin report screens

### Integration Hardening

Status: `completed`

- integration notes for BFF and internal consumers
- documented auth and error contracts
- Flutter-style event ingestion support
- `metadata` ingestion into event `properties`
- numeric identifier normalization for event ingestion

## Next Tasks

### 2. Aggregate Operations Visibility

Status: `next`

- add rollup status tracking
- show last successful hourly and daily rollups in admin
- add safe rerun and backfill controls

### 3. Lifecycle Controls in Admin

Status: `next`

- add activate, pause, and deactivate actions
- add archive or delete flows for inventory records

### 4. Placement Resolution Troubleshooting

Status: `next`

- build a screen to explain why a placement did not resolve
- surface rule failures such as inactive target, expired campaign, or scope mismatch

### 5. Placement and Creative Compatibility Rules

Status: `next`

- enforce placement allowed creative types during admin save
- enforce the same rule during runtime resolution

### 6. Reporting Improvements

Status: `backlog`

- CSV export
- richer operational filters
- clearer report empty states

### 7. Admin Security and Governance

Status: `backlog`

- roles and permissions
- audit logging
- operator capability separation

### 8. Platform Operations

Status: `backlog`

- health and readiness endpoints
- monitoring and alerting
- campaign expiry warnings
- broken asset checks
