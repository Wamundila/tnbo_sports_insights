# Football Tournament Inline Placement

Please add a new sponsor placement for football tournament pages:

- placement_id: `football_tournament_inline_1`
- service: `match_center`
- surface: `football_tournament_page`
- intended BFF page route: `GET /api/bff/pages/football-tournament?slug={tournamentSlug}`
- intended use: inline sponsor card/banner within a football tournament detail page

Expected BFF placement resolve request:

```json
{
  "user_id": null,
  "anonymous_id": "anon_device_id",
  "session_id": "sess_current_session",
  "platform": "android",
  "service": "match_center",
  "surface": "football_tournament_page",
  "screen_name": "FootballTournamentScreen",
  "context": {
    "competition_id": "mtn-faz-super-league",
    "content_id": "mtn-faz-super-league",
    "content_type": "football_tournament"
  },
  "placements": ["football_tournament_inline_1"]
}
```

The placement should be eligible without requiring a specific match id. It may optionally support competition/tournament targeting through `competition_id`.

BFF will inject the returned creative as a generic `sponsor_block`, so please return the same placement response shape already used for other sponsor placements.
