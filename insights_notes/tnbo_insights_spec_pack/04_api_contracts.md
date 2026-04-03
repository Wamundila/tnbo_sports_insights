# 04. API Contracts

## Goal

Define practical API contracts between Flutter, BFF, and TNBO Insights Service.

## Recommended flow

### Analytics ingestion
Flutter -> BFF -> TNBO Insights

### Sponsor placement retrieval
Flutter requests a screen/feed from BFF  
BFF -> TNBO Insights for eligible sponsor placements  
BFF merges sponsor blocks into response  
Flutter renders blocks and tracks outcomes

---

## A. Analytics ingestion endpoints

### 1. POST `/api/v1/events/batch`
Primary ingestion endpoint.

#### Request
```json
{
  "source": "flutter_app",
  "schema_version": 1,
  "events": [
    {
      "event_id": "evt_001",
      "event_name": "screen_view",
      "occurred_at": "2026-04-03T10:10:00Z",
      "service": "news",
      "surface": "home",
      "screen_name": "NewsHomeScreen",
      "user_id": "ts_1",
      "anonymous_id": "anon_ab12",
      "session_id": "sess_001",
      "platform": "android",
      "app_version": "1.0.0",
      "block_id": null,
      "block_type": null,
      "placement_id": null,
      "position_index": null,
      "content_id": null,
      "content_type": null,
      "campaign_id": null,
      "creative_id": null,
      "match_id": null,
      "competition_id": null,
      "team_id": null,
      "properties": {
        "entry_point": "app_launch"
      }
    }
  ]
}
```

#### Response
```json
{
  "accepted": true,
  "received_count": 1,
  "stored_count": 1,
  "invalid_count": 0,
  "errors": []
}
```

### Validation rules
- reject unknown top-level structure
- optionally allow unknown `properties` keys
- deduplicate by `event_id`

---

## B. Sponsor placement resolution endpoints

### 2. POST `/api/v1/placements/resolve`
Used by BFF to request sponsor blocks for a given surface/context.

#### Request
```json
{
  "user_id": "ts_1",
  "anonymous_id": "anon_ab12",
  "session_id": "sess_001",
  "platform": "android",
  "service": "match_center",
  "surface": "match_detail",
  "screen_name": "MatchDetailScreen",
  "context": {
    "match_id": "match_5541",
    "competition_id": "super_league_2026",
    "home_team_id": "zesco_united",
    "away_team_id": "nkana_fc"
  },
  "placements": [
    "match_center_header_companion",
    "match_center_inline_1"
  ]
}
```

#### Response
```json
{
  "placements": [
    {
      "placement_id": "match_center_header_companion",
      "served_event": {
        "event_name": "campaign_served",
        "campaign_id": "cmp_2026_001",
        "creative_id": "creative_01"
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
        "cta_url": "https://example.com/zamtel"
      }
    }
  ]
}
```

---

## C. Reporting endpoints

### 3. GET `/api/v1/reports/overview`
For internal dashboard summary.

Query examples:
- date range
- service
- surface
- campaign

Response includes:
- DAU / WAU / MAU summary
- top surfaces
- top blocks
- top content
- top sponsor campaigns

### 4. GET `/api/v1/reports/campaigns/{campaignId}`
Sponsor/campaign report.

Response may include:
- impressions
- unique reach
- clicks
- CTR
- by placement
- by date
- by service
- by surface

### 5. GET `/api/v1/reports/content`
Content performance report.

### 6. GET `/api/v1/reports/live`
Live commentary / live match report.

---

## D. Campaign management endpoints

These are admin-facing and can be internal-only initially.

### 7. POST `/api/v1/sponsors`
Create sponsor.

### 8. POST `/api/v1/campaigns`
Create campaign.

### 9. POST `/api/v1/campaigns/{campaignId}/creatives`
Create creative.

### 10. POST `/api/v1/placements`
Create placement inventory slot.

### 11. POST `/api/v1/campaigns/{campaignId}/targets`
Attach campaign to placements.

### 12. PATCH `/api/v1/campaigns/{campaignId}/status`
Activate/pause campaign.

---

## E. BFF-facing contracts

BFF should keep its existing surface/feed endpoints, but include sponsor placement integration.

Examples in the current TNBO app shape:
- GET `/api/bff/pages/home`
- GET `/api/bff/pages/article?slug={slug}`
- GET `/api/bff/pages/games`
- GET `/api/bff/pages/watch`
- GET `/api/bff/pages/match-center`
- GET `/api/bff/pages/football-tournament?slug={slug}`
- GET `/api/bff/pages/football-match?id={id}`
- GET `/api/bff/interactive/trivia/...`
- GET `/api/bff/interactive/predictor/...`
- GET `/api/bff/interactive/polls/...`

Internally, BFF:
1. fetches content from domain services
2. requests placements from TNBO Insights
3. injects sponsor blocks into its final response

---

## F. Error handling guidance

### Event ingestion
Never let analytics failures break the main user experience.
Use:
- best-effort ingestion
- queue retries
- dead-letter logging for malformed batches

### Placement resolution
If sponsor resolution fails:
- return content without sponsor blocks
- do not block screen rendering

---

## G. Security and auth guidance

### For app -> BFF
Use existing app auth/token flow.

### For BFF -> Insights
Use service-to-service auth:
- shared secret
- internal token
- signed request header
- or internal network restriction

Do not require Flutter to hold the Insights service key.

### For admin reporting endpoints
Require privileged admin roles.

---

## H. Idempotency and deduplication

Use `event_id` as an idempotency key.
This protects against:
- app retries
- flaky network resubmissions
- duplicate event batches

---

## I. Response performance guidance

Placement resolution should be fast.
Suggestions:
- cache active placements and campaign targets
- precompute eligible campaigns
- minimize DB joins at request time

---

## J. Contract versioning

Version public/internal endpoints:
- `/api/v1/...`

Version event contract separately with:
- `schema_version`
