# 10. Schema Ecosystem Alignment

Read this file first, then use the rest of the schema pack.

If any earlier file conflicts with this note, follow this note.

## 1. Insights Stores Analytics And Sponsorship Data, Not App Users

The TNBO app already uses AuthBox identities like:

- `ts_1`
- `ts_200`

So schema design should assume:

- `user_id` is an external AuthBox-backed id
- there is no local Insights users table acting as the source of truth
- `user_id` should be stored as a string, not assumed numeric

## 2. Keep Cross-Service References Logical, Not Relational

Insights will reference entities owned by other services, but it should not own them.

Examples:

- News owns article records
- Match Center owns matches, competitions, and teams
- Media owns streams and watch items
- Interactive owns trivia, predictor, and polls

So in Insights:

- store external ids as strings
- do not add foreign keys to other services' databases
- do not require those services to change schema just for Insights

This applies to fields such as:

- `content_id`
- `match_id`
- `competition_id`
- `team_id`

## 3. Surface Naming Must Match The Existing TNBO App

For page-builder surfaces already in use, prefer:

- `home_page`
- `article_page`
- `games_page`
- `watch_page`
- `match_center_page`
- `football_tournament_page`
- `football_match_page`

For dedicated feature surfaces, use stable names like:

- `trivia_dashboard`
- `trivia_results`
- `predictor_dashboard`
- `poll_detail`
- `media_detail`

## 4. Service Naming Must Stay Consistent

Use:

- `news`
- `match_center`
- `media`
- `interactive`
- `insights`
- `sponsors`

Do not use drifted values like `matchcenter`.

## 5. Block And Placement Context Must Match The Current BFF

The current TNBO app already has stable page-builder block identifiers.

For BFF/page-builder analytics:

- `block_id` should map to BFF `instance_key`
- `block_type` should map to BFF `template_key`

Examples:

- `block_id = hero_top_stories`
- `block_type = news_articles`

- `block_id = home_daily_trivia`
- `block_type = daily_trivia`

- `block_id = watch_page_surface`
- `block_type = media_watch_surface`

This is the context Insights should expect from BFF and Flutter.

## 6. Placement Inventory Should Match Real TNBO Surfaces

Use placement codes that match the app we actually have, for example:

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

## 7. Sponsor Delivery Logs Should Support BFF Page Composition Context

`campaign_delivery_logs` and sponsor-related analytics should be able to record:

- `service`
- `surface`
- `placement_id`
- `block_id`
- `block_type`
- `content_id` where relevant

The schema does not need a special foreign key relationship for this. It just needs stable indexed fields.

## 8. Anonymous And Session Context Are First-Class

The app already depends on:

- `anonymous_id`
- `session_id`

So these should remain first-class indexed dimensions in the schema.

Do not model analytics as authenticated-user-only.

## 9. BFF Is The Trusted Normalizer

Because BFF already knows:

- page keys
- route context
- block instance keys
- block template keys
- authenticated user claims

the schema should assume most event rows arrive with normalized context from BFF, not raw inconsistent client/service shapes.

## 10. What The Schema Agent Should Treat As Required

- keep `user_id` as external AuthBox `ts_*` ids
- keep cross-service identifiers as reference strings
- do not add foreign keys to News, Match Center, Media, or Interactive
- align `surface` values to the real TNBO page keys and feature surfaces
- align `block_id` to BFF `instance_key`
- align `block_type` to BFF `template_key`
- use `match_center`, not `matchcenter`
