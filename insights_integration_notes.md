# TNBO Insights Integration Notes

This document describes the TNBO Insights service APIs intended for internal consumers such as BFF and trusted backend services.

## Base URL

Examples below assume the service is available at:

```text
https://insights.internal.tnbo
```

## Authentication

All `/api/v1/*` routes are protected with a service-to-service API key.

Send this header on every API request:

```http
X-API-Key: your-shared-service-api-key
```

Environment variables used by the service:

```env
INSIGHTS_API_KEY=replace-with-a-strong-random-value
INSIGHTS_API_KEY_HEADER=X-API-Key
INSIGHTS_REPORTING_TIMEZONE=Africa/Lusaka
```

Important:

- Do not expose this key to Flutter/mobile clients.
- Flutter should continue sending analytics through BFF.
- BFF should call TNBO Insights using the internal `X-API-Key` header.

## Time Handling

Clients and BFF should send `occurred_at` in UTC, preferably as an ISO-8601 timestamp ending in `Z`.

TNBO Insights stores the raw event instant in UTC, then derives the report `event_date` using `INSIGHTS_REPORTING_TIMEZONE` (`Africa/Lusaka` by default). This means an event at `2026-04-03T22:30:00Z` is stored as a UTC event but appears in reports for `2026-04-04` in Zambia.

For existing production rows created before this rule, run:

```bash
php artisan insights:repair-event-dates --from-date=2026-04-01 --to-date=2026-04-22
php artisan insights:rollup-daily --date=2026-04-03
php artisan insights:rollup-today
```

Use a broad enough `--from-date`/`--to-date` range because the repair command filters by stored UTC `occurred_at` date. Repeat `insights:rollup-daily --date=...` for every affected reporting date, including the day before and day after the repaired window when events may have shifted across midnight.

## Admin Auth Boundary

There are two separate auth boundaries in this service:

- `/api/v1/*` routes are API-key protected and are intended for BFF and other trusted backend consumers
- `/admin/*` routes are browser-based admin pages protected by Laravel session auth for human operators

Important:

- the browser admin does not require or use `X-API-Key`
- the Blade admin talks to internal controllers and services directly inside the Laravel app
- BFF and other services should never depend on the browser admin for integration

## Integration Model

Recommended production flow:

```text
Flutter App -> BFF -> TNBO Insights
```

BFF responsibilities:

1. Normalize trusted context such as `user_id`, `surface`, `block_id`, and `block_type`.
2. Proxy analytics batches to Insights.
3. Request eligible placements from Insights.
4. Inject sponsor blocks into the final BFF payload returned to Flutter.

## Naming Rules

Use these stable values when integrating:

- `service`: `news`, `match_center`, `media`, `interactive`, `insights`, `sponsors`
- use `match_center`, not `matchcenter`
- page-builder `surface` values should match TNBO page keys such as:
  - `home_page`
  - `article_page`
  - `games_page`
  - `watch_page`
  - `match_center_page`
  - `football_tournament_page`
  - `football_match_page`
- `block_id` should map to BFF `instance_key`
- `block_type` should map to BFF `template_key`
- `user_id` should be the AuthBox external id such as `ts_1`

## Core APIs

### 1. Batch Event Ingestion

Internal route:

```http
POST /api/v1/events/batch
```

Headers:

```http
Content-Type: application/json
X-API-Key: your-shared-service-api-key
```

Request:

```json
{
  "schema_version": 1,
  "events": [
    {
      "event_id": "evt_1001",
      "event_name": "screen_view",
      "occurred_at": "2026-04-03T10:10:00Z",
      "service": "news",
      "surface": "home_page",
      "screen_name": "NewsHomeScreen",
      "user_id": "ts_1",
      "anonymous_id": "anon_ab12",
      "session_id": "sess_001",
      "device_id": "device_001",
      "platform": "android",
      "app_version": "1.0.0",
      "block_id": "hero_top_stories",
      "block_type": "news_articles",
      "placement_id": null,
      "position_index": 1,
      "content_id": null,
      "content_type": null,
      "campaign_id": null,
      "creative_id": null,
      "match_id": null,
      "competition_id": null,
      "team_id": null,
      "metadata": {
        "entry_point": "app_launch",
        "tab": "top_stories"
      },
      "properties": {
        "session_source": "app_launch"
      }
    }
  ]
}
```

Response:

```json
{
  "accepted": true,
  "received_count": 1,
  "stored_count": 1,
  "invalid_count": 0,
  "errors": []
}
```

Behavior:

- `source` is optional
- `event_id` is the idempotency key
- send `occurred_at` in UTC; report dates are derived in the Insights reporting timezone
- duplicate replays are accepted but not re-stored
- unknown top-level event fields are rejected with `422`
- `anonymous_id` is required even when `user_id` is present
- `metadata` is accepted as an alias for additional event context and is stored inside `properties`
- if both `metadata` and `properties` are sent, both are merged into the stored `properties` JSON
- identifier fields such as `content_id`, `match_id`, `competition_id`, and `team_id` may be sent as strings or integers

Flutter-style payloads proxied through BFF are valid, for example:

```json
{
  "schema_version": 1,
  "events": [
    {
      "event_id": "evt_b87ec3dccb8819d6",
      "event_name": "match_open",
      "occurred_at": "2026-04-03T15:34:06.544558Z",
      "service": "match_center",
      "surface": "football_tournament_page",
      "screen_name": "FootballTournamentPageScreen",
      "anonymous_id": "anon_7e5058a24bcb1389",
      "session_id": "sess_ebdcd8ef2d538631",
      "platform": "android",
      "app_version": "1.0.0+1",
      "block_id": "tournament_fixtures",
      "block_type": "football_tournament_fixtures",
      "content_type": "football_fixture",
      "match_id": 196,
      "competition_id": 3,
      "metadata": {
        "competition_slug": "tnbo-league",
        "content_presentation_type": "vertical_card_stack"
      }
    }
  ]
}
```

Typical BFF proxy example:

```bash
curl -X POST "https://insights.internal.tnbo/api/v1/events/batch" \
  -H "Content-Type: application/json" \
  -H "X-API-Key: ${INSIGHTS_API_KEY}" \
  -d @events.json
```

### 2. Placement Resolution

Internal route:

```http
POST /api/v1/placements/resolve
```

Purpose:

- BFF uses this during page composition
- BFF sends the requested placement codes for the current surface
- Insights returns eligible sponsor creative payloads
- Insights also logs `campaign_served` internally

Request:

```json
{
  "user_id": "ts_1",
  "anonymous_id": "anon_ab12",
  "session_id": "sess_001",
  "platform": "android",
  "service": "match_center",
  "surface": "match_center_page",
  "screen_name": "MatchDetailScreen",
  "context": {
    "match_id": "match_5541",
    "competition_id": "super_league_2026",
    "content_id": "match_5541",
    "content_type": "football_match"
  },
  "placements": [
    "match_center_header_companion",
    "match_center_inline_1"
  ]
}
```

Response:

```json
{
  "placements": [
    {
      "placement_id": "match_center_header_companion",
      "served_event": {
        "event_name": "campaign_served",
        "event_id": "served_90eb2a79-20b5-4c39-a62f-b54ec9d39d8b",
        "campaign_id": "cmp_2026_001",
        "creative_id": "creative_01",
        "delivery_id": "90eb2a79-20b5-4c39-a62f-b54ec9d39d8b"
      },
      "creative": {
        "campaign_id": "cmp_2026_001",
        "creative_id": "creative_01",
        "creative_type": "sponsor_card",
        "label_text": "Sponsored",
        "title": "Zamtel MatchDay Partner",
        "body": "Stay connected through every match day.",
        "image_url": "https://cdn.example.com/zamtel-card.jpg",
        "logo_url": "https://cdn.example.com/zamtel-logo.png",
        "cta_text": "Learn more",
        "cta_url": "https://example.com/zamtel",
        "metadata": null
      }
    }
  ]
}
```

BFF should:

1. inject the returned creative into the correct page-builder block structure
2. preserve the returned `campaign_id`, `creative_id`, `placement_id`, and `delivery_id`
3. ensure Flutter includes those values in later sponsor render/view/click events

Sponsor follow-up events should include sponsor context as top-level fields where possible:

```json
{
  "event_name": "sponsor_impression",
  "campaign_id": "cmp_2026_001",
  "creative_id": "creative_01",
  "placement_id": "home_inline_1",
  "block_id": "home_inline_1",
  "block_type": "sponsor_card",
  "properties": {
    "delivery_id": "delivery_123",
    "visible_duration_ms": 1400,
    "visibility_percent": 80
  }
}
```

If older clients send `campaign_id`, `creative_id`, `placement_id`, `block_id`, or `block_type` inside `properties`, Insights promotes those values into the indexed event columns during ingestion.

Recognized sponsor reporting events:

- `campaign_served`: Insights selected and returned the campaign
- `sponsor_block_rendered` or `sponsor_rendered`: the client rendered the sponsor block
- `sponsor_block_view` or `sponsor_impression`: the block became a qualified impression
- `sponsor_click` or `sponsor_cta_click`: the user clicked or activated the sponsor CTA

Recommended fallback:

- if Insights returns an empty `placements` array, BFF should return the page without sponsor blocks
- if Insights is unavailable, BFF should fail open for the page response

Partial-success behavior is intentional:

- when multiple placement codes are requested, Insights returns only the placements that resolved successfully
- placements with no eligible campaign are omitted from the response
- one unresolved slot does not fail the whole placement response

Example partial-success response:

```json
{
  "placements": [
    {
      "placement_id": "home_inline_1",
      "served_event": {
        "event_name": "campaign_served",
        "event_id": "served_123",
        "campaign_id": "cmp_2026_001",
        "creative_id": "creative_01",
        "delivery_id": "delivery_123"
      },
      "creative": {
        "campaign_id": "cmp_2026_001",
        "creative_id": "creative_01",
        "creative_type": "sponsor_card",
        "label_text": "Sponsored",
        "title": "Partner Message",
        "body": "Example body",
        "image_url": "https://cdn.example.com/image.jpg",
        "logo_url": null,
        "cta_text": "Learn more",
        "cta_url": "https://example.com",
        "metadata": null
      }
    }
  ]
}
```

### 3. Overview Report

Internal route:

```http
GET /api/v1/reports/overview
```

Query params:

- `date_from`
- `date_to`
- `service` optional
- `surface` optional
- `campaign_id` optional

Example:

```bash
curl "https://insights.internal.tnbo/api/v1/reports/overview?date_from=2026-04-01&date_to=2026-04-07&service=news" \
  -H "X-API-Key: ${INSIGHTS_API_KEY}"
```

Example response:

```json
{
  "date_range": {
    "from": "2026-04-01",
    "to": "2026-04-07"
  },
  "summary": {
    "screen_views": 16420,
    "sessions": 8120,
    "unique_users": 5930,
    "sponsor_impressions": 2750,
    "sponsor_clicks": 132
  },
  "active_users": {
    "dau": 980,
    "wau": 5930,
    "mau": 12810
  },
  "top_surfaces": [],
  "top_blocks": [],
  "top_campaigns": []
}
```

### 4. Campaign Report

Internal route:

```http
GET /api/v1/reports/campaigns/{campaignCode}
```

Example:

```bash
curl "https://insights.internal.tnbo/api/v1/reports/campaigns/cmp_2026_001?date_from=2026-04-01&date_to=2026-04-30" \
  -H "X-API-Key: ${INSIGHTS_API_KEY}"
```

Example response:

```json
{
  "campaign_id": "cmp_2026_001",
  "campaign_name": "Zamtel MatchDay Partner",
  "sponsor_name": "Zamtel",
  "date_range": {
    "from": "2026-04-01",
    "to": "2026-04-30"
  },
  "summary": {
    "served_count": 12000,
    "rendered_count": 11000,
    "qualified_impressions": 9400,
    "clicks": 410,
    "ctr": 0.0436,
    "unique_users_reached": 5100
  },
  "by_placement": [],
  "by_date": []
}
```

### 5. Content Report

Internal route:

```http
GET /api/v1/reports/content
```

Query params:

- `date_from`
- `date_to`
- `service` optional
- `content_type` optional
- `limit` optional

Example response:

```json
{
  "date_range": {
    "from": "2026-04-01",
    "to": "2026-04-07"
  },
  "items": [
    {
      "service": "news",
      "content_type": "article",
      "content_id": "article_981",
      "opens": 420,
      "unique_users": 300,
      "completions": 96,
      "shares": 18,
      "avg_engagement_seconds": 122.5
    }
  ]
}
```

### 6. Live Report

Internal route:

```http
GET /api/v1/reports/live
```

Query params:

- `date_from`
- `date_to`
- `service` optional, usually `media` or `match_center`
- `match_id` optional
- `limit` optional

Example response:

```json
{
  "date_range": {
    "from": "2026-04-03",
    "to": "2026-04-03"
  },
  "summary": {
    "audio_starts": 640,
    "listen_seconds_total": 182400,
    "sponsor_impressions": 3100,
    "sponsor_clicks": 84
  },
  "items": []
}
```

## Admin / Backoffice APIs

These routes are also protected by `X-API-Key`, but they are intended for trusted internal tooling, scripts, or future admin automation, not Flutter.

Routes:

- `GET /api/v1/admin/sponsors`
- `POST /api/v1/admin/sponsors`
- `GET /api/v1/admin/placements`
- `POST /api/v1/admin/placements`
- `GET /api/v1/admin/campaigns`
- `POST /api/v1/admin/campaigns`
- `GET /api/v1/admin/creatives`
- `POST /api/v1/admin/creatives`
- `GET /api/v1/admin/targets`
- `POST /api/v1/admin/targets`

### Create Sponsor

```http
POST /api/v1/admin/sponsors
```

Request:

```json
{
  "code": "zamtel",
  "name": "Zamtel",
  "status": "active",
  "website_url": "https://example.com",
  "contact_name": "Commercial Team",
  "contact_email": "ads@example.com",
  "notes": "Launch sponsor"
}
```

Response:

```json
{
  "data": {
    "id": 1,
    "code": "zamtel",
    "name": "Zamtel",
    "status": "active",
    "website_url": "https://example.com",
    "contact_name": "Commercial Team",
    "contact_email": "ads@example.com",
    "notes": "Launch sponsor",
    "created_at": "2026-04-03T09:00:00.000000Z",
    "updated_at": "2026-04-03T09:00:00.000000Z"
  }
}
```

## Error Responses

All important API errors include a stable machine-readable `code` field.

Documented codes:

- `UNAUTHORIZED`
- `VALIDATION_ERROR`
- `EVENT_BATCH_INVALID`
- `EVENT_BATCH_TOO_LARGE`
- `PLACEMENT_RESOLUTION_FAILED`
- `REPORT_NOT_FOUND`
- `API_KEY_NOT_CONFIGURED`

### Unauthorized

Missing or wrong API key:

```json
{
  "message": "Unauthorized.",
  "code": "UNAUTHORIZED"
}
```

Status:

```http
401 Unauthorized
```

### Validation Error

Example:

```json
{
  "message": "The events.0.unexpected_field field is not allowed.",
  "code": "EVENT_BATCH_INVALID",
  "errors": {
    "events.0.unexpected_field": [
      "The events.0.unexpected_field field is not allowed."
    ]
  }
}
```

Status:

```http
422 Unprocessable Entity
```

### Event Batch Too Large

```json
{
  "message": "Event batch exceeds the maximum size of 1000 events.",
  "code": "EVENT_BATCH_TOO_LARGE",
  "errors": {
    "events": [
      "The events field must not have more than 1000 items."
    ]
  }
}
```

### Placement Resolution Failure

If placement resolution hits an internal error:

```json
{
  "message": "Placement resolution failed.",
  "code": "PLACEMENT_RESOLUTION_FAILED"
}
```

### Report Not Found

If a report target does not exist, for example an unknown campaign code:

```json
{
  "message": "Report not found.",
  "code": "REPORT_NOT_FOUND"
}
```

## Current Starter Placements

These are seeded in the service today:

- `home_inline_1`
- `article_inline_1`
- `games_inline_1`
- `watch_inline_1`
- `match_center_header_companion`

## Current Web Admin

The service also includes a Blade-based admin UI for human operators:

- `/admin/login`
- `/admin/dashboard`
- `/admin/sponsors`
- `/admin/placements`
- `/admin/campaigns`
- `/admin/creatives`
- `/admin/targets`
- `/admin/reports/*`

This web admin uses Laravel session auth and is separate from the API-key-protected service routes.
