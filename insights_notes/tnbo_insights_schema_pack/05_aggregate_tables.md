# 05. Aggregate Tables

Dashboards and sponsor reports should read from aggregates, not scan raw events every time.

## 5.1 agg_hourly_service_metrics
Used for near-live monitoring by service.

### Suggested dimensions
- `metric_hour`
- `service`
- `platform`

### Suggested metrics
- sessions
- unique_users
- screen_views
- content_opens
- sponsor_impressions
- sponsor_clicks
- audio_starts
- audio_listen_seconds
- game_starts
- poll_votes

## 5.2 agg_daily_surface_metrics
Daily totals by service/surface.

### Dimensions
- `metric_date`
- `service`
- `surface`
- `platform`

### Metrics
- sessions
- unique_users
- screen_views
- avg_time_spent_seconds
- exits
- sponsor_impressions
- sponsor_clicks

## 5.3 agg_daily_block_metrics
Measures how blocks/sections perform.

### Dimensions
- `metric_date`
- `service`
- `surface`
- `block_id`
- `block_type`
- `placement_id` nullable

### Metrics
- block_views
- block_clicks
- unique_viewers
- sponsor_impressions
- sponsor_clicks
- ctr

## 5.4 agg_daily_content_metrics
Useful for articles, videos, matches, commentary streams, and games.

### Dimensions
- `metric_date`
- `service`
- `content_type`
- `content_id`

### Metrics
- opens
- unique_users
- completions
- shares
- avg_engagement_seconds

## 5.5 agg_daily_campaign_metrics
Main sponsor reporting table.

### Dimensions
- `metric_date`
- `sponsor_id`
- `campaign_id`
- `creative_id`
- `placement_id`
- `service`
- `surface`

### Metrics
- served_count
- rendered_count
- qualified_impressions
- unique_reach
- clicks
- ctr
- completions
- spend_estimate nullable

## 5.6 agg_daily_user_metrics
Useful for retention and user growth.

### Dimensions
- `metric_date`
- `platform`

### Metrics
- dau
- new_users
- returning_users
- avg_sessions_per_user
- avg_session_duration_seconds

## Rollup jobs
Recommended scheduler jobs:
- every 5 minutes: hourly incremental rollup
- hourly: backfill previous hour
- daily at 00:15 or 01:00: previous-day finalization
- nightly: repair/backfill for late events
