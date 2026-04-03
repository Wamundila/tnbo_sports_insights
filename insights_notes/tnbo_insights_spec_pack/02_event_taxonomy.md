# 02. Event Taxonomy

## Goal

Define a shared analytics language used across Flutter, BFF, and TNBO services.

## Required event envelope

Every event should include the following top-level fields.

| Field | Type | Notes |
|---|---|---|
| `event_id` | string | UUID generated client-side or BFF-side |
| `event_name` | string | Controlled vocabulary |
| `occurred_at` | datetime | ISO-8601 timestamp |
| `service` | string | `news`, `media`, `match_center`, `interactive`, `insights`, `sponsors` |
| `surface` | string | Logical screen or app surface |
| `screen_name` | string | Rendered screen name |
| `user_id` | string/null | Logged-in user if available |
| `anonymous_id` | string | Persistent anonymous/device identity |
| `session_id` | string | Session identity |
| `device_id` | string/null | Optional stable device identifier |
| `platform` | string | `android`, `ios`, `web` |
| `app_version` | string | App version/build |
| `properties` | object | Event-specific data |

## Context fields

Use the following where relevant:

| Field | Example |
|---|---|
| `block_id` | `hero_top_stories` |
| `block_type` | `news_articles`, `daily_trivia`, `single_choice_poll`, `sponsor_card` |
| `placement_id` | `home_inline_1` |
| `position_index` | `3` |
| `content_id` | `article_123` |
| `content_type` | `article`, `video`, `audio_stream`, `poll`, `prediction_game` |
| `campaign_id` | `cmp_2026_001` |
| `creative_id` | `creative_02` |
| `match_id` | `match_5541` |
| `competition_id` | `super_league_2026` |
| `team_id` | `zesco_united` |

## Event categories

### A. App and session events
Use to measure broad usage and retention.

- `app_open`
- `session_start`
- `session_end`
- `screen_view`
- `screen_exit`
- `search`
- `share`
- `bookmark_add`
- `bookmark_remove`

### B. Block and item visibility
Use to measure surface and placement performance.

- `block_view`
- `block_expand`
- `item_impression`
- `item_click`

### C. News events
- `article_open`
- `article_scroll_depth`
- `article_complete`
- `article_share`

Suggested properties:
- `scroll_percent`
- `article_word_count`
- `read_time_seconds`
- `author_id`
- `category_slug`

### D. Media events
#### Video / originals
- `media_open`
- `video_play`
- `video_pause`
- `video_progress`
- `video_complete`

#### Live commentary / audio
- `audio_player_open`
- `audio_play`
- `audio_pause`
- `audio_stop`
- `audio_heartbeat`
- `audio_error`

Suggested properties:
- `stream_id`
- `mount_name`
- `current_position_seconds`
- `heartbeat_interval_seconds`
- `listen_seconds_total`

### E. MatchCenter events
- `match_center_open`
- `fixture_open`
- `timeline_tab_view`
- `stats_tab_view`
- `lineup_tab_view`
- `lineup_expand`
- `standing_view`
- `team_page_open`

Suggested properties:
- `match_state` (`upcoming`, `live`, `finished`)
- `minute_of_match`
- `home_team_id`
- `away_team_id`

### F. Interactive events
- `game_open`
- `game_start`
- `question_answered`
- `game_complete`
- `poll_view`
- `poll_vote`
- `prediction_open`
- `prediction_submitted`
- `leaderboard_view`

Suggested properties:
- `game_type`
- `question_id`
- `selected_option_id`
- `score`
- `competition_phase`

### G. Sponsor and campaign events
- `campaign_served`
- `sponsor_block_rendered`
- `sponsor_block_view`
- `sponsor_click`
- `sponsor_cta_click`
- `sponsor_dismiss`
- `sponsor_video_start`
- `sponsor_video_complete`

## Important definitions

### Served
Sponsor creative was included in a response payload.

### Rendered
Sponsor creative was rendered by the app.

### Viewed / qualified impression
Sponsor creative was visible enough to count as an impression.

Recommended v1 rule:
- at least **50% visible**
- for at least **1 second**

### Click
User tapped/clicked on sponsor creative or CTA.

## Screen, block, action model

TNBO should measure at three levels.

### 1. Screen metrics
Answers:
- how many users reached this screen?
- how often?
- how long did they stay?

### 2. Block metrics
Answers:
- which areas of the screen get seen?
- what inventory is valuable?
- where should sponsors be placed?

### 3. Action metrics
Answers:
- what did users actually do?
- what drove engagement?
- what converted?

## Event naming rules

Use:
- lowercase
- snake_case
- verbs for actions
- nouns where identity is needed inside properties

Good:
- `article_open`
- `poll_vote`
- `sponsor_cta_click`

Avoid:
- `articleOpened`
- `clickedArticle`
- `user_did_article_click`

## Required baseline events for v1

At minimum, implement these first:

### Across all surfaces
- `session_start`
- `screen_view`
- `block_view`
- `item_click`

### News
- `article_open`

### Media
- `audio_play`
- `audio_heartbeat`
- `video_play`

### MatchCenter
- `match_center_open`

### Interactive
- `game_start`
- `game_complete`
- `poll_vote`

### Sponsors
- `campaign_served`
- `sponsor_block_view`
- `sponsor_click`

## Heartbeat guidance

For live commentary/audio, use a heartbeat event to measure actual listening time.

Suggested v1 rule:
- emit `audio_heartbeat` every 15 or 30 seconds while actively playing
- include cumulative listened seconds
- stop when paused or disconnected

This is much more reliable than measuring only play starts.

## Identity guidance

A user may be:
- authenticated
- anonymous
- previously anonymous then later authenticated

For v1:
- store both `anonymous_id` and `user_id`
- do not overcomplicate identity stitching initially
- allow later reconciliation if needed

## Event versioning

Add:
- `schema_version`

This allows event evolution without breaking consumers.

Suggested start:
- `schema_version = 1`

## Alignment with the current TNBO app

For page-builder screens already in use, prefer:

- `surface = home_page`
- `surface = article_page`
- `surface = games_page`
- `surface = watch_page`
- `surface = match_center_page`
- `surface = football_tournament_page`
- `surface = football_match_page`

For BFF-driven page-builder surfaces:

- `block_id` should map to BFF `instance_key`
- `block_type` should map to BFF `template_key`
