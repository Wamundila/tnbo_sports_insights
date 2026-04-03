# 04. Indexes and Constraints

## General principles
- Prioritize write safety on raw event tables
- Add indexes only for likely reporting filters
- Put wide and less predictable data into JSON columns
- Use composite indexes where date + dimension filtering is common

## Recommended indexes

### analytics_sessions
- unique: `session_id`
- index: `user_id`
- index: `anonymous_id`
- index: `started_at`

### analytics_events
- unique: `event_uuid`
- index: `occurred_at`
- index: `event_date`
- index: `event_name`
- index: `service`
- index: `surface`
- index: `user_id`
- index: `session_id`
- index: `campaign_id`
- index: `placement_id`
- index: `content_id`
- composite: `(service, event_date)`
- composite: `(service, surface, event_date)`
- composite: `(campaign_id, event_date)`
- composite: `(placement_id, event_date)`
- composite: `(content_type, content_id, event_date)`

### placements
- unique: `code`
- composite: `(service, surface, is_active)`

### campaigns
- unique: `slug`
- composite: `(status, start_at, end_at)`
- composite: `(sponsor_id, status)`

### campaign_targets
- composite: `(campaign_id, placement_id)`
- composite: `(placement_id, is_active)`

### campaign_delivery_logs
- unique: `delivery_uuid`
- composite: `(campaign_id, served_at)`
- composite: `(placement_id, served_at)`
- composite: `(service, surface, served_at)`

## Foreign key guidance
Use foreign keys for inventory and aggregate domains, but be careful with high-volume raw event ingestion.

Recommended:
- strict FKs on `campaign_creatives`, `campaign_targets`, `placement_rules`
- optional or nullable FKs on raw event references

Reason:
- mobile analytics payloads may contain stale or delayed identifiers
- ingestion should not fail too often because of reference drift

## Partitioning note
If volume grows substantially, partition `analytics_events` by month or by event_date range.

For early phase:
- start without partitioning
- archive old data when necessary
- move to monthly partitions after real volume appears
