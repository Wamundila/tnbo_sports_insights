# 07. Reporting and Dashboards

## Goal

Define the internal and sponsor-facing metrics TNBO should report from the new system.

## Dashboard categories

### 1. Executive overview
Used by founders/strategy/commercial teams.

Metrics:
- DAU / WAU / MAU
- new vs returning users
- sessions
- top services by engagement
- top surfaces by reach
- total sponsor impressions
- total sponsor clicks
- total sponsor revenue context if added later

### 2. Product analytics dashboard
Used by product/editorial teams.

Metrics:
- screen views by service/surface
- block performance by surface
- article opens and completion
- game starts and completion
- matchcenter engagement
- live commentary listening time

### 3. Live coverage dashboard
Used during active matchdays.

Metrics:
- concurrent listeners
- live audio starts
- total listening minutes
- top live matches
- matchcenter opens by match
- sponsor impressions during live windows

### 4. Sponsor campaign dashboard
Used by sales/commercial team and for sponsor reporting.

Metrics:
- served
- rendered
- qualified impressions
- clicks
- CTR
- unique reach
- by placement
- by date
- by service
- by surface
- by match/competition context where available

## Core TNBO product KPIs

### Audience growth
- DAU
- WAU
- MAU
- new users
- returning users
- stickiness ratio (`DAU / MAU`)

### Engagement
- sessions per user
- average session duration
- screen views per session
- article reads
- game completion rate
- live commentary listen seconds
- repeat participation rate

### Content performance
- article open rate
- article completion rate
- media starts and completion rate
- matchcenter opens by match
- poll participation rate

### Sponsor performance
- qualified impressions
- clicks
- CTR
- unique reach
- top placements
- top environments

## Recommended sponsor report format

### Summary section
- campaign name
- sponsor name
- campaign dates
- total qualified impressions
- unique users reached
- clicks
- CTR

### Breakdown section
- by service
- by surface
- by placement
- by date
- by match/competition context

### Narrative section
Short human-friendly interpretation, for example:
- “The campaign performed strongest on live match surfaces.”
- “Commentary companion placements produced the longest engaged exposure window.”

## Why sponsor reports must use qualified impressions

Using only “served” is weaker and less defensible. A sponsor wants confidence that:
- the ad was actually shown
- not merely included in an API response

So dashboard definitions must clearly distinguish:
- served
- rendered
- qualified impression

## Internal dashboard views to prioritize first

### View 1: Surface performance
Columns:
- service
- surface
- screen views
- unique users
- avg engagement seconds

### View 2: Block performance
Columns:
- service
- surface
- block_id / placement_id
- views
- clicks
- CTR where relevant

### View 3: Campaign performance
Columns:
- campaign
- placement
- impressions
- clicks
- CTR
- unique users

### View 4: Live commentary performance
Columns:
- date
- match
- stream
- unique listeners
- listen minutes
- sponsor impressions

## Attribution cautions

Do not overclaim sponsor outcomes in v1.
Stick to first-party metrics you can defend:
- exposure
- clicks
- engagement near exposure
- context

Avoid claims like conversions or sales impact unless there is a clean integration to prove it.

## Recommended report time grains

- hourly for live operations
- daily for standard reporting
- weekly for strategic trend review
- campaign lifetime for commercial reporting

## Segmentation ideas for later

Not necessary for v1, but future useful cuts:
- platform
- logged-in vs anonymous
- new vs returning
- competition
- club/team interest
- geographic region
- highly engaged fans vs casual users

## Data trust practices

Each dashboard metric should have a clear definition.
Example:

### Qualified impression
A sponsor creative counted when:
- rendered on screen
- 50% visible or more
- visible for at least 1 second

Write these definitions down in code comments and docs so reporting remains consistent.
