# 03. Table Catalog

## 3.1 analytics_sessions
Tracks app sessions to support session metrics and join events efficiently.

### Suggested columns
- `id`
- `session_id` unique
- `user_id` nullable
- `anonymous_id` nullable
- `device_id` nullable
- `platform` nullable
- `app_version` nullable
- `country_code` nullable
- `city` nullable
- `started_at`
- `ended_at` nullable
- `created_at`
- `updated_at`

## 3.2 analytics_events
Append-only source-of-truth table for all user and sponsor events.

### Suggested columns
- `id`
- `event_uuid` unique
- `session_id` indexed nullable
- `user_id` nullable indexed
- `anonymous_id` nullable indexed
- `device_id` nullable indexed
- `platform` nullable
- `app_version` nullable
- `event_name` indexed
- `event_category` indexed nullable
- `service` indexed
- `surface` indexed nullable
- `screen_name` nullable
- `block_id` nullable indexed
- `block_type` nullable
- `placement_id` nullable indexed
- `position_index` nullable
- `content_id` nullable indexed
- `content_type` nullable
- `match_id` nullable indexed
- `competition_id` nullable indexed
- `team_id` nullable indexed
- `sponsor_id` nullable indexed
- `campaign_id` nullable indexed
- `creative_id` nullable indexed
- `occurred_at` indexed
- `event_date` indexed
- `properties` JSON nullable
- `created_at`
- `updated_at`

## 3.3 analytics_event_dedup
Tracks ingestion idempotency so repeated client retries do not double count.

### Suggested columns
- `id`
- `event_uuid` unique
- `first_seen_at`
- `created_at`
- `updated_at`

## 3.4 placements
Represents sponsor inventory slots in the app.

### Suggested columns
- `id`
- `code` unique
- `name`
- `service` indexed
- `surface` indexed
- `block_type`
- `allowed_creative_type`
- `position_hint` nullable
- `is_active` boolean
- `description` text nullable
- `created_at`
- `updated_at`

## 3.5 placement_rules
Rules for placement eligibility.

### Suggested columns
- `id`
- `placement_id` foreign key
- `rule_type`
- `rule_operator`
- `rule_value` JSON
- `priority` default 0
- `is_active` boolean
- `created_at`
- `updated_at`

## 3.6 sponsors
Sponsor master records.

### Suggested columns
- `id`
- `name`
- `slug` unique
- `contact_name` nullable
- `contact_email` nullable
- `website_url` nullable
- `status` indexed
- `notes` text nullable
- `created_at`
- `updated_at`

## 3.7 campaigns
Sponsor campaign master table.

### Suggested columns
- `id`
- `sponsor_id` foreign key
- `name`
- `slug` unique
- `objective` nullable
- `status` indexed
- `start_at` indexed
- `end_at` indexed
- `budget_amount` nullable
- `priority` default 0
- `frequency_cap_per_user_per_day` nullable
- `targeting_config` JSON nullable
- `reporting_label` nullable
- `created_at`
- `updated_at`

## 3.8 campaign_creatives
Creatives attached to a campaign.

### Suggested columns
- `id`
- `campaign_id` foreign key
- `creative_type` indexed
- `title` nullable
- `body` text nullable
- `label` nullable
- `image_url` nullable
- `video_url` nullable
- `audio_url` nullable
- `cta_text` nullable
- `cta_url` nullable
- `destination_type` nullable
- `metadata` JSON nullable
- `is_active` boolean
- `created_at`
- `updated_at`

## 3.9 campaign_targets
Links campaigns to placements and optional service/surface context.

### Suggested columns
- `id`
- `campaign_id` foreign key
- `placement_id` foreign key
- `service` nullable indexed
- `surface` nullable indexed
- `priority` default 0
- `max_impressions` nullable
- `max_clicks` nullable
- `is_active` boolean
- `created_at`
- `updated_at`

## 3.10 campaign_delivery_logs
One record for each sponsor item delivered in a response.

### Suggested columns
- `id`
- `delivery_uuid` unique
- `campaign_id` foreign key nullable
- `creative_id` foreign key nullable
- `placement_id` foreign key nullable
- `session_id` nullable indexed
- `user_id` nullable indexed
- `anonymous_id` nullable indexed
- `service` indexed
- `surface` nullable indexed
- `served_at` indexed
- `response_context` JSON nullable
- `created_at`
- `updated_at`

## 3.11 sponsor_block_events
Optional fast-path table for sponsor-only actions if you want a smaller table for sponsor reporting.

### Suggested columns
- `id`
- `event_uuid` unique
- `delivery_log_id` nullable foreign key
- `campaign_id` nullable indexed
- `creative_id` nullable indexed
- `placement_id` nullable indexed
- `session_id` nullable indexed
- `user_id` nullable indexed
- `anonymous_id` nullable indexed
- `event_name` indexed
- `service` indexed
- `surface` nullable indexed
- `occurred_at` indexed
- `properties` JSON nullable
- `created_at`
- `updated_at`

This can mirror sponsor events from `analytics_events` or be omitted if one-table analytics is preferred.
