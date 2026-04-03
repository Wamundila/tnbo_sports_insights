# 08. Rollout Plan

## Goal

Ship a reliable first-party analytics and sponsor measurement system without overengineering.

## Phase 1 — Foundations

### Deliverables
- TNBO Insights Laravel app created
- raw event ingestion endpoint
- event validation and deduplication
- `analytics_events` table
- first aggregate jobs
- placements table
- campaigns, creatives, sponsors tables
- BFF integration for placement resolution
- Flutter tracking for core screen and sponsor events

### Minimum events
- `session_start`
- `screen_view`
- `block_view`
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

### Outcome
TNBO gets a trustworthy baseline for:
- user activity
- screen usage
- sponsor delivery
- sponsor clicks
- live commentary listening

## Phase 2 — Operational reporting

### Deliverables
- daily and hourly dashboards
- campaign performance endpoints
- live commentary reporting
- block-level dashboard
- better session summaries

### Outcome
The team can:
- monitor product behaviour
- create sponsor reports
- identify strong placements

## Phase 3 — Optimization

### Deliverables
- frequency capping
- improved placement targeting
- better campaign scheduling
- richer content environment metrics
- experiment support for placements

### Outcome
TNBO can optimize sponsor inventory and improve commercial packaging.

## Phase 4 — Advanced audience intelligence

### Possible later additions
- retention cohorts
- audience segments
- recommendation signals
- notification targeting
- personalized content ordering

## Recommended implementation order

1. create Insights service skeleton
2. implement `analytics_events` ingestion
3. implement BFF proxy for events
4. implement `screen_view` and `session_start`
5. implement sponsor placements resolution
6. implement sponsor render/view/click tracking
7. implement news and match_center core events
8. implement commentary heartbeat events
9. implement interactive game/poll events
10. add aggregates and dashboards

## Success criteria for first release

- events arrive reliably
- duplicates are prevented
- dashboards show sensible numbers
- sponsor placements can be inserted into BFF responses
- sponsor reports can show impressions, clicks, and placement breakdowns
- live commentary listen time is measurable

## Risks and mitigations

### Risk: too many events too quickly
Mitigation:
- batch ingestion
- queue writes
- reduce non-essential events initially

### Risk: inconsistent event naming
Mitigation:
- one shared event taxonomy
- central analytics client in Flutter
- BFF/Insights validation

### Risk: sponsor numbers not trusted
Mitigation:
- use explicit qualified impression logic
- document metric definitions
- distinguish served/rendered/viewed

### Risk: analytics breaks app experience
Mitigation:
- best-effort async sending
- do not block main UI flows
- allow graceful fallback on placement failure

## Suggested staffing split

### Flutter / mobile
- event emission
- visibility tracking
- sponsor rendering

### BFF / backend
- event proxy
- placement resolution integration
- response composition

### Insights service
- ingestion
- schema validation
- storage
- aggregation
- campaign reporting

### Commercial / product
- placement naming
- sponsor package design
- dashboard review
