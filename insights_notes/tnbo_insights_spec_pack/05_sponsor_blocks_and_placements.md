# 05. Sponsor Blocks and Placements

## Goal

Define the first sponsor inventory model for TNBO.

## Principle

Do not hardcode sponsor creatives separately inside News, MatchCenter, Media, and Interactive.

Instead, define **placements** centrally and serve **creatives** into those placements.

## Placement concept

A placement is a predefined slot on a surface where a sponsor creative may appear.

Examples:
- `home_top_banner`
- `home_inline_1`
- `article_inline_1`
- `article_footer_card`
- `match_center_header_companion`
- `commentary_player_banner`
- `interactive_results_sponsor`

Each placement belongs to:
- a service
- a surface
- a position/context

## Recommended v1 sponsor block types

Start with a small set.

### 1. Image banner
Best for:
- home
- article inline
- commentary screens

Fields:
- label_text
- image_url
- cta_url
- cta_text

### 2. Sponsor card
Best for:
- match center
- article footer
- interactive result screens

Fields:
- label_text
- title
- body
- image_url
- logo_url
- cta_text
- cta_url

### 3. Sponsored content tile
Looks similar to a normal content tile but clearly marked `Sponsored`.

Fields:
- label_text
- title
- description
- thumbnail_url
- destination_url

### 4. Audio commentary companion
Shown on commentary surface while audio is active.

Fields:
- label_text
- title
- body
- image_url
- logo_url
- cta_url

## Placement catalogue suggestion for v1

### A. Home / app feed
- `home_top_banner`
- `home_inline_1`
- `home_inline_2`

### B. News
- `article_inline_1`
- `article_footer_card`

### C. MatchCenter
- `match_center_header_companion`
- `match_center_inline_1`

### D. Media / commentary
- `commentary_player_banner`
- `video_prelist_card`

### E. Interactive
- `game_start_sponsor`
- `game_completion_sponsor`
- `poll_results_sponsor`

## Placement rules

Each placement can define:
- allowed creative types
- max creatives shown per response
- frequency caps
- service/surface restrictions
- optional competition or match targeting
- optional user segment constraints later

Example placement rule:
- `match_center_header_companion`
  - only `sponsor_card` or `image_banner`
  - max 1 creative at a time
  - allowed on `match_center:match_detail`

## Campaign targeting model

Campaigns can target:
- one or more placements
- one or more services
- one or more competitions
- optionally match contexts
- optional date/time windows

Examples:
- sponsor only Super League commentary screens
- sponsor only Zesco United match pages
- sponsor all game completion screens

## Impression logic

For sponsor reporting, count at least these layers:

### Served
Creative returned in placement resolution API response.

### Rendered
Creative was mounted/rendered in the UI.

### Qualified impression
Creative was actually visible.

Recommended v1 visibility rule:
- >= 50% in viewport
- >= 1 second visible

### Click
User taps sponsor container or CTA.

## Recommended sponsor KPIs

### Delivery KPIs
- served count
- rendered count
- qualified impressions
- unique reach
- clicks
- CTR

### Context KPIs
- by service
- by surface
- by placement
- by competition
- by match
- by content environment

### Quality KPIs
- average time near sponsor block
- audio listening minutes while companion block displayed
- video completion rate for sponsor creatives if used later

## Labeling and trust

Every sponsor creative should be clearly labeled with something like:
- `Sponsored`
- `Partner Message`
- `Brought to you by`

Keep labeling visible to preserve trust.

## Frequency capping

Not mandatory for first build, but recommended design support:
- per user/day max impressions per campaign
- per session max repeated exposure

Example:
- max 3 impressions per campaign per user per day
- max 1 impression per screen load for same placement

## Sponsor reporting examples

A sponsor may ask:
- How many impressions did our campaign get?
- How many unique users saw it?
- Which screens performed best?
- What was the CTR?
- How many listening sessions occurred while our commentary sponsor card was shown?

Your data model should support those questions directly.

## Why placement metrics matter

Page/screen metrics alone cannot tell you:
- whether the sponsor was placed high or low
- whether the slot is actually visible
- whether some placements outperform others

Block/placement metrics are essential to price inventory well.

## Why page/screen metrics still matter

Sponsors may also want context like:
- “This campaign appeared on match detail screens with high attention”
- “This campaign ran next to live commentary coverage”
- “This campaign appeared in news article environments”

So both screen and placement context matter.

## Recommended first sponsor inventory package to sell

A simple early TNBO sponsor offering could be:

### Package A: MatchDay visibility
Includes:
- `match_center_header_companion`
- `commentary_player_banner`

### Package B: Fan engagement package
Includes:
- `game_start_sponsor`
- `game_completion_sponsor`
- `poll_results_sponsor`

### Package C: Editorial reach package
Includes:
- `home_inline_1`
- `article_inline_1`
- `article_footer_card`

Those packages become easier to explain once placements are standardized.
