# 09. Tasks

## Schema
- [ ] Create foundational migrations for sessions and events
- [ ] Create sponsor inventory migrations
- [ ] Create campaign delivery and sponsor block event migrations
- [ ] Create aggregate table migrations

## Backend
- [ ] Build `/api/v1/events/ingest`
- [ ] Build `/api/v1/placements/resolve`
- [ ] Build campaign CRUD endpoints for admin use
- [ ] Build sponsor reporting endpoints using aggregates

## Data processing
- [ ] Add idempotency check via `event_uuid`
- [ ] Add queue jobs for event enrichment if needed
- [ ] Add hourly rollup job
- [ ] Add daily finalization job
- [ ] Add backfill / repair command

## App / BFF integration
- [ ] Add shared event contract document
- [ ] Add BFF proxy to Insights ingestion endpoint
- [ ] Add BFF placement resolution call
- [ ] Insert sponsor blocks into feed responses
- [ ] Emit sponsor rendered/view/click events from Flutter

## Validation
- [ ] Create seed data for placements and sponsor campaign examples
- [ ] Create dashboard sanity checks for counts
- [ ] Compare served vs rendered vs viewed counts
- [ ] Validate CTR calculations
