# 02. Migration Order

Use this migration order to avoid foreign-key problems and to keep the first release focused.

## Phase A — foundational tables
1. `create_analytics_sessions_table`
2. `create_analytics_events_table`
3. `create_analytics_event_dedup_table`

## Phase B — placement and sponsor inventory
4. `create_placements_table`
5. `create_placement_rules_table`
6. `create_sponsors_table`
7. `create_campaigns_table`
8. `create_campaign_creatives_table`
9. `create_campaign_targets_table`

## Phase C — delivery and sponsor tracking
10. `create_campaign_delivery_logs_table`
11. `create_sponsor_block_events_table`

## Phase D — aggregates
12. `create_agg_hourly_service_metrics_table`
13. `create_agg_daily_surface_metrics_table`
14. `create_agg_daily_block_metrics_table`
15. `create_agg_daily_content_metrics_table`
16. `create_agg_daily_campaign_metrics_table`
17. `create_agg_daily_user_metrics_table`

## Notes
- Keep raw event tables in the first release even if some aggregate tables come later
- If write volume becomes high, move aggregation off request path immediately
- Aggregates can be rebuilt from raw events if necessary, which is why raw event integrity matters most
