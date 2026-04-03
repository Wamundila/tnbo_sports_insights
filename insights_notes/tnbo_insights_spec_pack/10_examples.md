# 10. Examples

## Example 1 — News article open event

```json
{
  "event_id": "evt_1001",
  "schema_version": 1,
  "event_name": "article_open",
  "occurred_at": "2026-04-03T10:15:00Z",
  "service": "news",
  "surface": "article_detail",
  "screen_name": "ArticleDetailScreen",
  "user_id": "ts_1",
  "anonymous_id": "anon_ab12",
  "session_id": "sess_001",
  "platform": "android",
  "app_version": "1.0.0",
  "block_id": "top_stories_carousel",
  "block_type": "carousel",
  "placement_id": null,
  "position_index": 2,
  "content_id": "article_981",
  "content_type": "article",
  "campaign_id": null,
  "creative_id": null,
  "match_id": null,
  "competition_id": null,
  "team_id": null,
  "properties": {
    "category_slug": "super-league",
    "author_id": "author_8"
  }
}
```

## Example 2 — Audio heartbeat event

```json
{
  "event_id": "evt_2001",
  "schema_version": 1,
  "event_name": "audio_heartbeat",
  "occurred_at": "2026-04-03T10:20:30Z",
  "service": "media",
  "surface": "live_commentary_player",
  "screen_name": "CommentaryPlayerScreen",
  "user_id": "ts_1",
  "anonymous_id": "anon_ab12",
  "session_id": "sess_001",
  "platform": "android",
  "app_version": "1.0.0",
  "block_id": null,
  "block_type": null,
  "placement_id": "commentary_player_banner",
  "position_index": null,
  "content_id": "stream_tsac1_live",
  "content_type": "audio_stream",
  "campaign_id": "cmp_2026_001",
  "creative_id": "creative_01",
  "match_id": "match_5541",
  "competition_id": "super_league_2026",
  "team_id": null,
  "properties": {
    "stream_id": "tsac1",
    "mount_name": "live",
    "heartbeat_interval_seconds": 30,
    "listen_seconds_total": 180
  }
}
```

## Example 3 — Sponsor block view event

```json
{
  "event_id": "evt_3001",
  "schema_version": 1,
  "event_name": "sponsor_block_view",
  "occurred_at": "2026-04-03T10:25:00Z",
  "service": "match_center",
  "surface": "match_detail",
  "screen_name": "MatchDetailScreen",
  "user_id": "ts_1",
  "anonymous_id": "anon_ab12",
  "session_id": "sess_001",
  "platform": "android",
  "app_version": "1.0.0",
  "block_id": "sponsor_match_header",
  "block_type": "sponsor_card",
  "placement_id": "match_center_header_companion",
  "position_index": 1,
  "content_id": null,
  "content_type": null,
  "campaign_id": "cmp_2026_001",
  "creative_id": "creative_01",
  "match_id": "match_5541",
  "competition_id": "super_league_2026",
  "team_id": null,
  "properties": {
    "visibility_percent": 80,
    "visible_duration_ms": 1400
  }
}
```

## Example 4 — Placement resolution response

```json
{
  "placements": [
    {
      "placement_id": "home_inline_1",
      "creative": {
        "campaign_id": "cmp_2026_010",
        "creative_id": "creative_home_01",
        "creative_type": "image_banner",
        "label_text": "Sponsored",
        "image_url": "https://cdn.example.com/banner.jpg",
        "cta_text": "Visit sponsor",
        "cta_url": "https://example.com/sponsor"
      }
    }
  ]
}
```

## Example 5 — Mixed BFF surface response

```json
{
  "surface": "home",
  "blocks": [
    {
      "type": "editorial_hero",
      "block_id": "home_hero",
      "items": [
        { "content_id": "article_981", "title": "Big match preview" }
      ]
    },
    {
      "type": "sponsor_card",
      "block_id": "sponsor_home_inline_1",
      "placement_id": "home_inline_1",
      "campaign_id": "cmp_2026_010",
      "creative_id": "creative_home_01",
      "label_text": "Sponsored",
      "title": "Matchday Partner",
      "body": "Stay connected for every kickoff.",
      "image_url": "https://cdn.example.com/sponsor.jpg",
      "cta_text": "Learn more",
      "cta_url": "https://example.com/sponsor"
    },
    {
      "type": "article_list",
      "block_id": "latest_articles",
      "items": [
        { "content_id": "article_982", "title": "League update" }
      ]
    }
  ]
}
```

## Example 6 — Campaign report response shape

```json
{
  "campaign_id": "cmp_2026_001",
  "campaign_name": "Zamtel MatchDay Partner",
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
  "by_placement": [
    {
      "placement_id": "commentary_player_banner",
      "qualified_impressions": 4200,
      "clicks": 220,
      "ctr": 0.0524
    },
    {
      "placement_id": "match_center_header_companion",
      "qualified_impressions": 5200,
      "clicks": 190,
      "ctr": 0.0365
    }
  ]
}
```
