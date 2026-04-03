# 03. Data Model and Schema

## Goal

Define a practical database model for raw analytics, aggregates, sponsor inventory, campaigns, and reporting.

## Principles

- Raw events are append-only.
- Aggregates are recomputed or incrementally maintained.
- Sponsor inventory is modeled explicitly.
- Content identifiers stay external and reference upstream services logically.

---

## A. Raw analytics tables

### 1. `analytics_events`
Primary source of truth.

Suggested columns:

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `event_id` | string | Unique event UUID |
| `schema_version` | integer | Event schema version |
| `event_name` | string | Controlled event name |
| `occurred_at` | datetime | Event time |
| `service` | string | Domain service |
| `surface` | string | Logical screen/surface |
| `screen_name` | string | Displayed screen |
| `user_id` | string/null | Authenticated user |
| `anonymous_id` | string | Anonymous identity |
| `session_id` | string | Session identity |
| `device_id` | string/null | Optional device ID |
| `platform` | string | Android/iOS/Web |
| `app_version` | string/null | App version |
| `block_id` | string/null | Logical block |
| `block_type` | string/null | Block category |
| `placement_id` | string/null | Sponsor or content slot |
| `position_index` | integer/null | Position within block |
| `content_id` | string/null | Upstream content identifier |
| `content_type` | string/null | Content type |
| `campaign_id` | string/null | Campaign identifier |
| `creative_id` | string/null | Creative identifier |
| `match_id` | string/null | Match identifier |
| `competition_id` | string/null | Competition |
| `team_id` | string/null | Optional team reference |
| `properties_json` | json | Event-specific payload |
| `created_at` | datetime | Insert time |

Recommended indexes:
- unique `event_id`
- `(event_name, occurred_at)`
- `(service, occurred_at)`
- `(surface, occurred_at)`
- `(user_id, occurred_at)`
- `(anonymous_id, occurred_at)`
- `(session_id, occurred_at)`
- `(campaign_id, occurred_at)`
- `(placement_id, occurred_at)`
- `(content_id, occurred_at)`
- `(match_id, occurred_at)`

### 2. `analytics_ingest_batches` (optional)
Useful if app sends event batches.

Columns:
- `id`
- `batch_id`
- `source`
- `received_at`
- `event_count`
- `status`
- `errors_json`

---

## B. Session and identity support tables

### 3. `analytics_sessions`
Optional but useful if session summaries are materialized.

Columns:
- `id`
- `session_id`
- `user_id`
- `anonymous_id`
- `platform`
- `app_version`
- `started_at`
- `ended_at`
- `duration_seconds`
- `screen_views_count`
- `events_count`

### 4. `analytics_user_daily_metrics`
Materialized per user/day metrics for retention and stickiness.

Columns:
- `metric_date`
- `user_id` or `anonymous_id`
- `platform`
- `sessions_count`
- `screen_views_count`
- `articles_opened_count`
- `games_started_count`
- `games_completed_count`
- `audio_listen_seconds`
- `video_play_count`

---

## C. Aggregate reporting tables

These should power dashboards, not the raw events table.

### 5. `agg_daily_surface_metrics`
Measures screen/surface usage.

Columns:
- `metric_date`
- `service`
- `surface`
- `screen_name`
- `screen_views`
- `unique_users`
- `unique_sessions`
- `avg_time_on_surface_seconds`

### 6. `agg_daily_block_metrics`
Measures content and sponsor block performance.

Columns:
- `metric_date`
- `service`
- `surface`
- `block_id`
- `block_type`
- `placement_id`
- `block_views`
- `unique_users`
- `item_impressions`
- `item_clicks`

### 7. `agg_daily_content_metrics`
Measures article/video/game/poll/match-level interaction.

Columns:
- `metric_date`
- `service`
- `content_id`
- `content_type`
- `surface`
- `opens`
- `completions`
- `shares`
- `avg_engagement_seconds`

### 8. `agg_hourly_live_metrics`
Measures live commentary and live-match usage.

Columns:
- `metric_hour`
- `service`
- `surface`
- `match_id`
- `stream_id`
- `audio_starts`
- `unique_listeners`
- `listen_seconds_total`
- `avg_listen_seconds`

### 9. `agg_daily_campaign_metrics`
Sponsor and campaign reporting.

Columns:
- `metric_date`
- `campaign_id`
- `creative_id`
- `placement_id`
- `service`
- `surface`
- `served_count`
- `rendered_count`
- `qualified_impressions`
- `clicks`
- `ctr`
- `unique_users_reached`

### 10. `agg_daily_sponsor_inventory_metrics`
Placement value tracking over time.

Columns:
- `metric_date`
- `placement_id`
- `service`
- `surface`
- `available_serves`
- `actual_serves`
- `qualified_impressions`
- `clicks`
- `ctr`

---

## D. Sponsorship and campaign tables

### 11. `sponsors`
Columns:
- `id`
- `sponsor_code`
- `name`
- `status`
- `website_url`
- `contact_name`
- `contact_email`
- `notes`
- timestamps

### 12. `campaigns`
Columns:
- `id`
- `campaign_code`
- `sponsor_id`
- `name`
- `objective`
- `status` (`draft`, `active`, `paused`, `completed`)
- `start_at`
- `end_at`
- `budget_notes`
- `targeting_json`
- `frequency_cap_json`
- timestamps

### 13. `campaign_creatives`
Columns:
- `id`
- `campaign_id`
- `creative_code`
- `creative_type` (`image_banner`, `sponsor_card`, `sponsored_tile`, `audio_companion`)
- `title`
- `body`
- `label_text`
- `image_url`
- `logo_url`
- `cta_text`
- `cta_url`
- `metadata_json`
- `status`
- timestamps

### 14. `placements`
Defines the inventory slots available in TNBO surfaces.

Columns:
- `id`
- `placement_code`
- `service`
- `surface`
- `block_type`
- `screen_position`
- `description`
- `default_rules_json`
- `status`
- timestamps

Examples:
- `home_top_banner`
- `home_inline_1`
- `article_inline_1`
- `match_center_header_companion`
- `commentary_player_banner`
- `interactive_results_sponsor`

### 15. `campaign_placement_targets`
Many-to-many between campaigns and placements.

Columns:
- `id`
- `campaign_id`
- `placement_id`
- `priority`
- `weight`
- `constraints_json`
- timestamps

### 16. `campaign_delivery_logs`
Optional raw delivery trace.

Columns:
- `id`
- `campaign_id`
- `creative_id`
- `placement_id`
- `user_id`
- `anonymous_id`
- `session_id`
- `served_at`
- `context_json`

---

## E. Recommended ER relationship summary

- one sponsor has many campaigns
- one campaign has many creatives
- one campaign targets many placements
- one placement can have many campaign targets
- raw events may reference sponsor, campaign, creative, placement, content, match, competition

---

## Suggested partitioning/throttling notes

If event volume grows:
- partition raw events by month or date
- archive old raw events
- keep aggregates indefinitely or much longer
- use queue-based ingestion for bursts

---

## Example migration order

1. create `analytics_events`
2. create `analytics_sessions`
3. create `agg_daily_surface_metrics`
4. create `agg_daily_block_metrics`
5. create `agg_daily_content_metrics`
6. create `agg_hourly_live_metrics`
7. create `sponsors`
8. create `campaigns`
9. create `campaign_creatives`
10. create `placements`
11. create `campaign_placement_targets`
12. create `agg_daily_campaign_metrics`
13. create `campaign_delivery_logs`

---

## Recommended v1 database compromise

For v1, do not over-normalize event data. A single wide `analytics_events` table with indexed context fields plus `properties_json` is practical and easy to ship.

Normalization can come later if required.
