# 01. Overview and Architecture

## Objective

TNBO needs a cross-service system for:

1. **Audience and product analytics**
2. **Sponsor placement delivery and sponsor performance measurement**

The analytics must work across:
- Flutter mobile app
- BFF
- News service
- MatchCenter service
- Media service
- Interactive service

## Recommended architecture

```text
Flutter App
   |
   v
BFF (client-facing gateway)
   |------------------------------> News
   |------------------------------> MatchCenter
   |------------------------------> Media
   |------------------------------> Interactive
   |
   |----> TNBO Insights Service
              - Analytics ingestion
              - Event storage
              - Aggregations
              - Sponsor placements
              - Campaign delivery rules
              - Reporting APIs
```

## Service responsibilities

### Flutter app
Responsible for:
- screen view events
- block visibility events
- action events
- media progress/heartbeat events
- sponsor visibility and click events

The app should not own business reporting logic.

### BFF
Responsible for:
- exposing a clean client-facing API
- attaching auth/user/session context
- forwarding analytics events to TNBO Insights
- requesting sponsor placements from TNBO Insights
- merging sponsor blocks into screen/feed responses

BFF should not permanently own:
- raw analytics storage
- aggregation jobs
- sponsor performance truth
- long-term reporting logic

### News / MatchCenter / Media / Interactive
Responsible for:
- content/domain data
- content metadata
- identifiers needed by analytics context

These services should not each invent separate analytics pipelines.

### TNBO Insights Service
Responsible for:
- receiving analytics events
- validating event schema
- storing raw events
- computing aggregates
- managing sponsor campaigns and placements
- serving sponsor block decisions
- exposing dashboards and sponsor reports

## Why not keep everything in BFF

Keeping analytics inside BFF is okay only as a very short transitional phase. Over time BFF becomes overloaded with:
- event ingestion
- reporting queries
- sponsor logic
- campaign calculations
- audience dashboards

That causes:
- domain confusion
- hard-to-maintain code
- limited reporting scalability
- weaker sponsor trust

## Why not keep analytics inside each service

That produces:
- inconsistent event names
- different definitions of impressions and views
- fragmented sponsor reporting
- difficulty calculating cross-service user behaviour

## Domain model

Think in this structure:

- **Service**
- **Surface / Screen**
- **Block / Placement**
- **Item / Content**
- **Action / Event**

Examples:
- Service: `news`
- Surface: `home`
- Block: `top_stories_carousel`
- Item: `article_981`
- Action: `article_open`

Sponsor example:
- Service: `match_center`
- Surface: `match_detail`
- Placement: `match_center_header_companion`
- Creative: `campaign_creative_44`
- Action: `sponsor_cta_click`

## Key design decisions

### 1. Use a central event contract
All services and app surfaces should use one shared contract for analytics.

### 2. Track screen, block, and action
Do not choose only page metrics or only block metrics. TNBO needs:
- screen reach
- block performance
- user intent/action metrics

### 3. Append raw events, report from aggregates
Raw events should be immutable.
Dashboards should read from daily/hourly aggregate tables.

### 4. Separate content from sponsorship
A sponsor placement is not just content. It is inventory with reporting, targeting, and campaign rules.

### 5. Measure visibility honestly
For sponsor reporting, distinguish:
- served = returned in API response
- rendered = drawn by app
- viewed = actually visible by rule
- clicked = user interacted

## Suggested first deployment shape

### Phase 1 shape
- Flutter sends events to BFF
- BFF forwards events to TNBO Insights
- BFF asks TNBO Insights for sponsor placements for each surface
- BFF merges sponsor blocks into its API response
- Flutter renders sponsor blocks and emits sponsor view/click events

### Later shape
Optionally allow direct app-to-Insights event ingestion for high volume flows, but only after the contract is stable.

## Suggested technology notes

Because the rest of the backend estate is Laravel, TNBO Insights can be Laravel as well.

Recommended Laravel concerns:
- API routes for ingestion and reporting
- queued jobs for aggregation
- scheduled commands for daily/hourly summaries
- Redis for transient counters and queue support
- relational DB for storage (MySQL/Postgres depending on stack preference)

## Non-goals for v1

Avoid these initially:
- full ad exchange complexity
- bidding engines
- advanced user segmentation
- multi-touch attribution
- complex recommendation engines
- deep CDP-style identity resolution

Start with reliable first-party analytics and simple sponsor inventory.
