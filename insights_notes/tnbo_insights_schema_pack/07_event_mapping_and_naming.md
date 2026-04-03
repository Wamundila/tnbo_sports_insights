# 07. Event Mapping and Naming

## Naming principles
- use stable snake_case names
- avoid per-service naming drift
- keep meaning precise

## Global events
- `app_open`
- `session_start`
- `session_end`
- `screen_view`
- `block_view`
- `item_impression`
- `item_click`
- `share`
- `search`

## News
- `article_open`
- `article_scroll_depth`
- `article_complete`

## Media
- `media_open`
- `audio_play`
- `audio_pause`
- `audio_stop`
- `audio_heartbeat`
- `video_play`
- `video_progress`
- `video_complete`

## Match center
- `match_center_open`
- `fixture_open`
- `lineup_expand`
- `stats_tab_view`
- `timeline_tab_view`

## Interactive
- `game_open`
- `game_start`
- `game_complete`
- `poll_view`
- `poll_vote`
- `prediction_submitted`

## Sponsor
- `campaign_served`
- `sponsor_block_rendered`
- `sponsor_block_view`
- `sponsor_click`
- `sponsor_cta_click`
- `sponsor_video_complete`

## Required payload fields
Every payload should aim to include:
- `event_uuid`
- `event_name`
- `occurred_at`
- `service`
- `surface`
- `session_id`
- `user_id` or `anonymous_id`

## Current TNBO app alignment

For page-builder surfaces already in use, prefer:

- `home_page`
- `article_page`
- `games_page`
- `watch_page`
- `match_center_page`
- `football_tournament_page`
- `football_match_page`

For BFF-driven page-builder analytics:

- `block_id` should map to BFF `instance_key`
- `block_type` should map to BFF `template_key`

## Strongly recommended optional fields
- `block_id`
- `block_type`
- `placement_id`
- `position_index`
- `content_id`
- `content_type`
- `campaign_id`
- `creative_id`
- `properties`
