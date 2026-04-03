# 08. Rollout Notes

## First release
Build the schema and endpoints for:
- raw event ingestion
- sponsor placement inventory
- campaign delivery logging
- basic daily campaign metrics

Do not delay launch waiting for advanced attribution.

## Safe rollout order
1. Create schema
2. Add ingestion endpoint in Insights
3. Proxy event calls through BFF
4. Start collecting only a handful of critical events
5. Validate data quality
6. Add sponsor placements and campaign selection
7. Add sponsor impression/click reporting
8. Expand event taxonomy gradually

## First events to instrument
- `screen_view`
- `article_open`
- `match_center_open`
- `audio_play`
- `audio_heartbeat`
- `game_start`
- `game_complete`
- `poll_vote`
- `campaign_served`
- `sponsor_block_view`
- `sponsor_click`

## What to avoid in first release
- over-normalizing every event dimension
- highly complex real-time dashboards
- too many sponsor block types
- querying raw events directly from admin dashboards
