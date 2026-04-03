# TNBO Insights Service — Laravel Migration / Schema Pack

This pack is a build-oriented follow-up to the implementation spec for the TNBO Insights Service.

It is designed for a new Laravel service that centralizes:
- cross-service analytics event ingestion
- aggregation for dashboards and sponsor reporting
- sponsor placements, creatives, campaigns, and delivery logs

Read this first:
- `10_schema_ecosystem_alignment.md`

## Goals
- Give the team a clean migration order
- Define the main tables and relationships
- Provide sample Laravel migration stubs
- Separate raw event capture from aggregate reporting
- Support sponsor impressions, clicks, and campaign reporting

## Recommended stack
- Laravel 11+
- MySQL 8+ or PostgreSQL 14+
- Queue workers for async aggregation and rollups
- Scheduler for hourly/daily summary jobs
- Redis for transient counters / cache

## Recommended first build scope
1. Raw event ingestion tables
2. Placement / campaign / creative tables
3. Delivery + sponsor event linkage
4. Hourly and daily aggregate tables
5. Materialized reporting endpoints on top of aggregates

## Pack contents
- `01_schema_summary.md`
- `02_migration_order.md`
- `03_table_catalog.md`
- `04_indexes_and_constraints.md`
- `05_aggregate_tables.md`
- `06_laravel_migration_blueprints.md`
- `07_event_mapping_and_naming.md`
- `08_rollout_notes.md`
- `09_tasks.md`
- `10_schema_ecosystem_alignment.md`
- `database/migrations/*.php`
- `reference_sql/mysql_schema_reference.sql`

## Domain split
The service is one Laravel app with two internal modules:
- Analytics
- Sponsorships / Placements

## Important design notes
- Treat `analytics_events` as append-only source-of-truth
- Do not rely on content services as analytics owners
- Use aggregates for dashboards; do not query raw events for every report
- Record both `served` and `viewed` sponsor events; they are not the same
- Standardize event names and context fields across all services
