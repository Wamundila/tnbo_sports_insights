# TNBO Insights Service — Markdown Implementation Spec Pack

This pack defines a practical implementation for:

1. **Cross-service user metrics** across Flutter, BFF, News, MatchCenter, Media, and Interactive.
2. **Sponsor placements and sponsor blocks** with measurable delivery and performance reporting.

## Recommended direction

Build a new Laravel service called **TNBO Insights Service** with two internal modules:

- **Analytics Module**
- **Sponsors / Placements Module**

Use the **BFF** as the client-facing gateway, but **do not** make BFF the permanent owner of analytics storage, aggregation, or campaign reporting.

Read this first:

- `11_system_integration_alignment.md` - required ecosystem alignment note for the current TNBO system

## Pack contents

- `01_overview_and_architecture.md` — system goals, boundaries, and architecture
- `02_event_taxonomy.md` — shared event naming and tracking model
- `03_data_model_and_schema.md` — suggested tables and schema direction
- `04_api_contracts.md` — ingestion, querying, placements, and reporting APIs
- `05_sponsor_blocks_and_placements.md` — sponsor inventory model and creative types
- `06_flutter_and_bff_integration.md` — practical implementation notes for app and BFF
- `07_reporting_and_dashboards.md` — internal dashboards and sponsor-facing reporting
- `08_rollout_plan.md` — phased rollout path
- `09_tasks.md` — task backlog for implementation
- `10_examples.md` — example payloads and reporting structures

- `11_system_integration_alignment.md` - required system-alignment adjustments for the existing TNBO ecosystem

## Core design principles

- Use a **single shared analytics contract** across all services.
- Track **screen + block + action** rather than choosing only one.
- Separate **raw events** from **aggregated reporting tables**.
- Distinguish between:
  - `served`
  - `rendered`
  - `viewed` / qualified impression
  - `clicked`
- Keep sponsor logic centralized so placement inventory and reporting remain trustworthy.

## Naming recommendation

Recommended service name: **TNBO Insights Service**

Alternative names:
- TNBO Analytics Service
- TNBO Audience Service
- TNBO SponsorHub

## Why this service should exist

Without a central analytics service, event shapes drift across apps and sponsor reports become hard to trust. A central service gives TNBO:

- one source of truth for usage metrics
- consistent sponsor performance reporting
- cross-service audience understanding
- a foundation for personalization, recommendations, and retention analysis later

## Suggested repository/package direction

If each backend is currently a separate Laravel app, TNBO Insights should also be a separate Laravel app for consistency.

Suggested internal modules:
- `App\Domains\Analytics`
- `App\Domains\Sponsors`
- `App\Domains\Reporting`

## Deliverable outcome

After implementing this pack, TNBO should be able to answer questions like:

- How many unique users opened MatchCenter this week?
- Which home screen blocks actually get attention?
- How many listening minutes did live commentary generate for a given match?
- Which sponsor placements deliver the highest CTR?
- Which competitions, articles, matches, or games create the strongest sponsor exposure?
