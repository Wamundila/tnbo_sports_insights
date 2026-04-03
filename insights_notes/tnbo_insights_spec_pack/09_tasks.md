# 09. Task Backlog

## Epic 1 — TNBO Insights service bootstrap

- [ ] Create new Laravel application for TNBO Insights
- [ ] Define service configuration and environment variables
- [ ] Configure DB, queue, cache, scheduler
- [ ] Add service-to-service auth for BFF -> Insights
- [ ] Create base domain folders: Analytics, Sponsors, Reporting

## Epic 2 — Analytics ingestion

- [ ] Create `POST /api/v1/events/batch`
- [ ] Validate incoming batch structure
- [ ] Enforce `event_id` deduplication
- [ ] Store raw events in `analytics_events`
- [ ] Add error logging for invalid events
- [ ] Add queue-backed ingestion if necessary
- [ ] Add ingest batch logging

## Epic 3 — Shared event contract

- [ ] Publish event taxonomy doc to engineering
- [ ] Freeze event naming rules for v1
- [ ] Define required/common event fields
- [ ] Add `schema_version`
- [ ] Create test fixtures for valid and invalid events

## Epic 4 — Sponsor model

- [ ] Create `sponsors` table
- [ ] Create `campaigns` table
- [ ] Create `campaign_creatives` table
- [ ] Create `placements` table
- [ ] Create `campaign_placement_targets` table
- [ ] Create simple admin CRUD or seeders for placements
- [ ] Define initial placement catalogue

## Epic 5 — Placement resolution

- [ ] Create `POST /api/v1/placements/resolve`
- [ ] Implement active campaign lookup
- [ ] Match creatives to requested placements
- [ ] Return normalized sponsor block payload
- [ ] Write delivery logs / served events
- [ ] Add fallback when no campaign is eligible

## Epic 6 — BFF integration

- [ ] Add `/api/bff/insights/events` endpoint in BFF
- [ ] Forward validated event batches to Insights
- [ ] Add internal placement resolution client in BFF
- [ ] Inject sponsor blocks into existing page-builder section/block responses
- [ ] Inject sponsor blocks into supported surfaces
- [ ] Add retry and timeout handling for Insights calls

## Epic 7 — Flutter analytics client

- [ ] Create central analytics client class
- [ ] Generate/store `anonymous_id`
- [ ] Generate/manage `session_id`
- [ ] Implement `screen_view`
- [ ] Implement `block_view`
- [ ] Implement `item_click`
- [ ] Implement sponsor render/view/click events
- [ ] Implement article open
- [ ] Implement match_center open/tab events
- [ ] Implement audio play/pause/heartbeat
- [ ] Implement game start/complete and poll vote

## Epic 8 — Aggregation jobs

- [ ] Build daily surface aggregates
- [ ] Build daily block aggregates
- [ ] Build daily campaign aggregates
- [ ] Build hourly live commentary aggregates
- [ ] Build user/session summary aggregates
- [ ] Schedule aggregation commands

## Epic 9 — Reporting

- [ ] Build internal overview report endpoint
- [ ] Build campaign report endpoint
- [ ] Build live commentary report endpoint
- [ ] Build block performance report endpoint
- [ ] Define report field names clearly
- [ ] Validate metric definitions with commercial team

## Epic 10 — Dashboard readiness

- [ ] Define DAU / WAU / MAU calculation
- [ ] Define qualified impression rule
- [ ] Define CTR formula
- [ ] Define live listening minutes calculation
- [ ] Write internal metric definition document
- [ ] Run QA against real test campaigns

## Epic 11 — Initial placements for launch

- [ ] `home_top_banner`
- [ ] `home_inline_1`
- [ ] `article_inline_1`
- [ ] `article_footer_card`
- [ ] `match_center_header_companion`
- [ ] `commentary_player_banner`
- [ ] `game_completion_sponsor`
- [ ] `poll_results_sponsor`

## Epic 12 — Sponsor launch readiness

- [ ] Prepare 3 sponsor block templates
- [ ] Prepare sponsor labels and disclosure rules
- [ ] Prepare sample campaign and creative seeds
- [ ] Prepare sponsor-facing campaign report format
- [ ] Prepare commercial explainer for placement packages
