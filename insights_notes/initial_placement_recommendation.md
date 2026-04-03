# Initial Placement Recommendation

This note is for the TNBO Insights agent.

## Decision

Do not block the Insights build on a fully finalized placement catalogue.

The service should be built now with:

- the placement model
- placement rules support
- campaign-to-placement targeting support
- a small starter catalogue

The full inventory can be expanded later without changing the core design.

## What Must Exist Now

Before implementation starts, define:

- placement naming rules
- the minimum fields a placement record needs
- a small initial set of real placements

That is enough to build:

- placement resolution
- sponsor delivery logging
- sponsor analytics
- BFF sponsor-block injection later

## Recommended Starter Placement Set

Use a small cross-surface starter set that reflects the TNBO app as it exists now:

- `home_inline_1`
- `article_inline_1`
- `games_inline_1`
- `watch_inline_1`
- `match_center_header_companion`

This set is enough to prove:

- editorial/feed placement insertion
- dedicated page placement insertion
- game/interactive sponsorship
- watch/media sponsorship
- match-context sponsorship

## Why Not Define Everything Up Front

If you try to finalize the entire inventory now, you will likely end up debating:

- commercial packaging
- exact screen positions
- future surfaces not yet built
- campaign strategy details

That should not delay the core service build.

The placement system should be broad enough to add more codes later without redesign.

## Placement Naming Guidance

Keep placement codes:

- lowercase
- snake_case
- surface-aware
- stable over time

Examples:

- `home_inline_1`
- `article_footer_card`
- `watch_inline_1`
- `match_center_inline_1`
- `trivia_results_sponsor`

Avoid vague names like:

- `slot_1`
- `banner_a`
- `promo_area`

## What Can Be Added Later

These can come after the initial build:

- `home_top_banner`
- `home_inline_2`
- `article_footer_card`
- `games_inline_2`
- `watch_top_banner`
- `match_center_inline_1`
- `football_tournament_inline_1`
- `football_match_inline_1`
- `commentary_player_banner`
- `trivia_results_sponsor`
- `predictor_dashboard_inline_1`
- `poll_results_sponsor`

## Build Recommendation

Proceed with the build using the starter placement set above.

Design the placement schema and APIs so that:

- new placement codes can be added later by admin/seed/config
- campaign targeting does not need redesign when new placements appear
- reporting works for both the starter set and future placements

## Practical Outcome

The correct approach is:

1. build the placement system now
2. seed or support a small starter catalogue
3. expand the placement inventory later as sponsor surfaces mature

So no, the full placement catalogue does not need to be finalized before the build starts.
