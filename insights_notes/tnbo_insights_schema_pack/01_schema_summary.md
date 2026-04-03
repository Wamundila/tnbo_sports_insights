# 01. Schema Summary

## Core design
The schema is organized into five layers:

1. **Reference / identity context**
   - normalized values used repeatedly across events and campaigns
2. **Raw analytics events**
   - immutable event stream from app activity
3. **Sponsor and placement inventory**
   - placements, campaigns, creatives, targeting rules
4. **Delivery / impression tracking**
   - served payloads, rendered blocks, sponsor actions
5. **Aggregates**
   - hourly/daily rollups for dashboards and sponsor reports

## Main table groups

### Analytics core
- `analytics_sessions`
- `analytics_events`
- `analytics_event_dedup`

### Sponsor inventory
- `placements`
- `placement_rules`
- `sponsors`
- `campaigns`
- `campaign_creatives`
- `campaign_targets`

### Delivery and impression tracking
- `campaign_delivery_logs`
- `sponsor_block_events`

### Aggregates
- `agg_hourly_service_metrics`
- `agg_daily_surface_metrics`
- `agg_daily_block_metrics`
- `agg_daily_content_metrics`
- `agg_daily_campaign_metrics`
- `agg_daily_user_metrics`

## Flow
1. Flutter app emits events via BFF
2. BFF forwards normalized payloads to Insights
3. Insights stores raw events in `analytics_events`
4. BFF requests eligible sponsor blocks from Insights
5. Insights logs campaign delivery in `campaign_delivery_logs`
6. Flutter sends sponsor render/view/click events
7. Scheduler rolls raw events into aggregates

## Minimum viable analytics dimensions
Each event should support these fields:
- event id
- event name
- occurred at
- user / anonymous / session identifiers
- service and surface
- block / placement / content context
- campaign and sponsor context where relevant
- properties JSON for flexible extension

## Minimum viable sponsor dimensions
- sponsor
- campaign
- creative
- placement
- served count
- qualified view count
- click count
- CTR
- date / hour
- service / surface context
