# 11. System Integration Alignment

Read this file first, then use the rest of the Insights pack.

If any earlier file conflicts with this note, follow this note.

## 1. Insights Is A Backend Service

TNBO Insights is a backend service inside the current TNBO ecosystem, not a second user-facing app.

For the TNBO Sports app, the path should remain:

```text
Flutter App -> BFF -> TNBO Insights
```

Insights owns:

- analytics ingestion
- raw event storage
- aggregates and reporting
- sponsor inventory
- campaign targeting and selection
- sponsor delivery logging

It should not become a second app gateway.

## 2. Keep Flutter Pointed At BFF

Flutter should not call TNBO Insights directly for normal app analytics and sponsorship flows.

This keeps Insights aligned with the existing backend estate:

- `Flutter -> BFF -> News`
- `Flutter -> BFF -> Match Center`
- `Flutter -> BFF -> Media`
- `Flutter -> BFF -> Interactive`
- `Flutter -> BFF -> Insights`

## 3. Route Naming Must Match Current BFF Conventions

Do not introduce a parallel `/api/app/...` route family for the TNBO Sports app.

Current app-facing routes already use:

- `/api/bff/pages/...`
- `/api/bff/...`

So the app-facing analytics ingestion route should be something like:

- `POST /api/bff/insights/events`

not:

- `POST /api/app/events`

Insights internal routes can still remain service-facing, for example:

- `POST /api/v1/events/batch`
- `POST /api/v1/placements/resolve`

## 4. Auth And Trust Model

### Flutter -> BFF

Use the existing app auth/session model.

### BFF -> Insights

Use service-to-service trust:

- internal service key
- internal bearer token
- signed request headers
- private networking

Do not require Flutter to hold the Insights service key.

## 5. AuthBox User Ids Are The Source Of Truth

Authenticated TNBO users already use AuthBox ids like:

- `ts_1`
- `ts_200`

So:

- `user_id` should be an AuthBox-backed external id
- do not create a local app-user table in Insights as the source of truth
- do not assume integer ids

## 6. Use Logical Domain Names, Not Drifted Variants

Recommended `service` values:

- `news`
- `match_center`
- `media`
- `interactive`
- `insights`
- `sponsors`

Use `match_center`, not `matchcenter`.

## 7. Surface Naming Should Match Real TNBO Surfaces

For page-builder screens already in use, prefer the real page keys:

- `home_page`
- `article_page`
- `games_page`
- `watch_page`
- `match_center_page`
- `football_tournament_page`
- `football_match_page`

For dedicated feature screens that are not page-builder pages yet, use stable feature surfaces such as:

- `trivia_dashboard`
- `trivia_results`
- `predictor_dashboard`
- `poll_detail`
- `media_detail`

## 8. Block Mapping Must Match Current BFF Structure

For page-builder surfaces:

- `block_id` should map to BFF `instance_key`
- `block_type` should map to BFF `template_key`

Examples:

- `block_id = hero_top_stories`
- `block_type = news_articles`

- `block_id = home_daily_trivia`
- `block_type = daily_trivia`

- `block_id = watch_page_surface`
- `block_type = media_watch_surface`

This is more stable than generic labels like `carousel`.

## 9. Sponsor Blocks Must Fit The Existing Page Builder

Sponsor placements should be designed so BFF can inject them into the same structure it already returns:

- `sections[].blocks[]`
- tab-based `sections[].tabs[].blocks[]`

That means sponsor payloads from Insights should be broad enough for BFF to convert into sponsor block templates such as:

- `sponsor_card`
- `sponsor_banner`
- `sponsored_tile`
- `audio_companion_sponsor`

Do not design sponsor output around a separate ad-only response model that bypasses the current page-builder shape.

## 10. Placement Resolution Is Internal To BFF Composition

Recommended flow:

1. BFF resolves the main page or feature response
2. BFF asks Insights for eligible sponsor placements
3. BFF injects sponsor blocks into the final app payload
4. Flutter renders sponsor blocks
5. Flutter emits render/view/click events back through BFF

Flutter should not request placements from Insights directly.

## 11. Placement Codes Should Match Real TNBO Surfaces

Recommended early placement examples:

- `home_top_banner`
- `home_inline_1`
- `article_inline_1`
- `article_footer_card`
- `games_inline_1`
- `watch_inline_1`
- `match_center_header_companion`
- `match_center_inline_1`
- `football_tournament_inline_1`
- `football_match_inline_1`
- `commentary_player_banner`
- `trivia_results_sponsor`
- `predictor_dashboard_inline_1`
- `poll_results_sponsor`

## 12. Insights Stores References, Not Other Services' Records

Insights should store:

- external content ids
- external match ids
- external competition ids
- external team ids
- campaign and placement ids

But it should not own News, Match Center, Media, or Interactive domain records.

So:

- store external ids as references
- do not add cross-service foreign keys
- do not require schema changes in those services just to support analytics

## 13. BFF Should Normalize Trusted Context

BFF already knows:

- authenticated user claims
- page keys
- route context
- block instance keys
- block template keys

So BFF should normalize trusted analytics context before forwarding to Insights.

## 14. What The Insights Agent Should Treat As Required

- build Insights as a backend service
- keep Flutter integrated through BFF
- use AuthBox `ts_*` user ids
- use `match_center`, not `matchcenter`
- align `surface` values to real page keys and feature surfaces
- treat `block_id` as BFF `instance_key`
- treat `block_type` as BFF `template_key`
- design sponsor payloads so BFF can inject them into the current page-builder response model
