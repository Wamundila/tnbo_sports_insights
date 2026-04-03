# 06. Laravel Migration Blueprints

This section explains how to implement the migrations in Laravel.

## Naming convention
Use timestamped files like:
- `2026_04_03_000001_create_analytics_sessions_table.php`
- `2026_04_03_000002_create_analytics_events_table.php`

## Column type guidance
- IDs: `id()` unless you already use UUIDs service-wide
- Event UUIDs / delivery UUIDs: `uuid()` or `string(36)`
- Flexible context: `json()`
- Time fields: `timestamp()` or `timestampTz()` depending on DB strategy
- Heavy text: `text()`
- short labels and enums: `string()`

## Raw event strategy
The raw table should be append-only.

Avoid:
- frequent updates to existing event rows
- hard dependencies that make ingestion brittle

Prefer:
- insert-only writes
- later rollups into aggregate tables
- replay-safe ingestion via `event_uuid`

## Example analytics_events considerations
- include `event_date` as a plain date column for easier rollups
- store non-core details under `properties`
- keep nullable references for optional contexts like match/team/campaign

## Example sponsor campaign considerations
- campaigns own timing, status, targeting config
- creatives own rendering assets and CTA metadata
- targets connect campaigns to placements

## Example aggregate considerations
- aggregate tables should have unique composite keys across their dimension columns
- store denormalized counts and averages for reporting speed

## Files in `database/migrations`
This pack includes starter migration stubs for:
- sessions
- events
- placements
- sponsors
- campaigns
- creatives
- targets
- delivery logs
- daily campaign metrics

They are examples, not a full final migration set.
