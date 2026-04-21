<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\AnalyticsSession;
use App\Models\Campaign;
use App\Models\Placement;
use App\Models\Sponsor;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingAndRollupTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rolls_up_metrics_and_serves_reports(): void
    {
        config()->set('insights.api_key', 'secret-token');

        $sponsor = Sponsor::query()->create([
            'code' => 'zamtel',
            'name' => 'Zamtel',
            'status' => 'active',
        ]);

        Campaign::query()->create([
            'sponsor_id' => $sponsor->id,
            'code' => 'cmp_2026_001',
            'name' => 'Zamtel MatchDay Partner',
            'status' => 'active',
        ]);

        Placement::query()->create([
            'code' => 'match_center_header_companion',
            'name' => 'Match Center Header Companion',
            'service' => 'match_center',
            'surface' => 'match_center_page',
            'block_type' => 'sponsor_card',
            'is_active' => true,
        ]);

        AnalyticsSession::query()->create([
            'session_id' => 'sess_001',
            'user_id' => 'ts_1',
            'anonymous_id' => 'anon_1',
            'platform' => 'android',
            'app_version' => '1.0.0',
            'started_at' => '2026-04-03 10:00:00',
            'ended_at' => '2026-04-03 10:20:00',
            'last_seen_at' => '2026-04-03 10:20:00',
        ]);

        $events = [
            [
                'event_id' => 'evt_screen',
                'event_name' => 'screen_view',
                'service' => 'match_center',
                'surface' => 'match_center_page',
                'screen_name' => 'MatchDetailScreen',
                'block_id' => null,
                'block_type' => null,
                'placement_id' => null,
                'content_id' => null,
                'content_type' => null,
                'campaign_id' => null,
                'creative_id' => null,
                'occurred_at' => '2026-04-03 10:00:00',
                'properties' => null,
            ],
            [
                'event_id' => 'evt_article_open',
                'event_name' => 'article_open',
                'service' => 'news',
                'surface' => 'article_page',
                'screen_name' => 'ArticlePage',
                'block_id' => 'hero_top_stories',
                'block_type' => 'news_articles',
                'placement_id' => null,
                'content_id' => 'article_981',
                'content_type' => 'article',
                'campaign_id' => null,
                'creative_id' => null,
                'occurred_at' => '2026-04-03 10:05:00',
                'properties' => ['read_time_seconds' => 120],
            ],
            [
                'event_id' => 'evt_news_home_screen',
                'event_name' => 'screen_view',
                'service' => 'news',
                'surface' => 'home_page',
                'screen_name' => 'HomePageScreen',
                'block_id' => null,
                'block_type' => null,
                'placement_id' => null,
                'content_id' => null,
                'content_type' => null,
                'campaign_id' => null,
                'creative_id' => null,
                'occurred_at' => '2026-04-03 10:05:30',
                'properties' => null,
                'user_id' => null,
                'anonymous_id' => 'anon_2',
            ],
            [
                'event_id' => 'evt_served',
                'event_name' => 'campaign_served',
                'service' => 'match_center',
                'surface' => 'match_center_page',
                'screen_name' => 'MatchDetailScreen',
                'block_id' => 'match_center_header_companion',
                'block_type' => 'sponsor_card',
                'placement_id' => 'match_center_header_companion',
                'content_id' => null,
                'content_type' => null,
                'campaign_id' => 'cmp_2026_001',
                'creative_id' => 'creative_01',
                'occurred_at' => '2026-04-03 10:06:00',
                'properties' => null,
            ],
            [
                'event_id' => 'evt_view',
                'event_name' => 'sponsor_block_view',
                'service' => 'match_center',
                'surface' => 'match_center_page',
                'screen_name' => 'MatchDetailScreen',
                'block_id' => 'match_center_header_companion',
                'block_type' => 'sponsor_card',
                'placement_id' => 'match_center_header_companion',
                'content_id' => null,
                'content_type' => null,
                'campaign_id' => 'cmp_2026_001',
                'creative_id' => 'creative_01',
                'occurred_at' => '2026-04-03 10:07:00',
                'properties' => ['visibility_percent' => 80, 'visible_duration_ms' => 1400],
            ],
            [
                'event_id' => 'evt_click',
                'event_name' => 'sponsor_click',
                'service' => 'match_center',
                'surface' => 'match_center_page',
                'screen_name' => 'MatchDetailScreen',
                'block_id' => 'match_center_header_companion',
                'block_type' => 'sponsor_card',
                'placement_id' => 'match_center_header_companion',
                'content_id' => null,
                'content_type' => null,
                'campaign_id' => 'cmp_2026_001',
                'creative_id' => 'creative_01',
                'occurred_at' => '2026-04-03 10:08:00',
                'properties' => null,
            ],
            [
                'event_id' => 'evt_audio_play',
                'event_name' => 'audio_play',
                'service' => 'media',
                'surface' => 'watch_page',
                'screen_name' => 'WatchScreen',
                'block_id' => null,
                'block_type' => null,
                'placement_id' => null,
                'content_id' => 'stream_1',
                'content_type' => 'audio_stream',
                'campaign_id' => null,
                'creative_id' => null,
                'occurred_at' => '2026-04-03 10:10:00',
                'properties' => null,
            ],
            [
                'event_id' => 'evt_audio_heartbeat',
                'event_name' => 'audio_heartbeat',
                'service' => 'media',
                'surface' => 'watch_page',
                'screen_name' => 'WatchScreen',
                'block_id' => null,
                'block_type' => null,
                'placement_id' => null,
                'content_id' => 'stream_1',
                'content_type' => 'audio_stream',
                'campaign_id' => null,
                'creative_id' => null,
                'occurred_at' => '2026-04-03 10:11:00',
                'properties' => ['heartbeat_interval_seconds' => 30, 'listen_seconds_total' => 30],
            ],
        ];

        foreach ($events as $event) {
            AnalyticsEvent::query()->create([
                'event_id' => $event['event_id'],
                'schema_version' => 1,
                'session_id' => 'sess_001',
                'user_id' => array_key_exists('user_id', $event) ? $event['user_id'] : 'ts_1',
                'anonymous_id' => array_key_exists('anonymous_id', $event) ? $event['anonymous_id'] : 'anon_1',
                'platform' => 'android',
                'app_version' => '1.0.0',
                'event_name' => $event['event_name'],
                'event_category' => 'test',
                'service' => $event['service'],
                'surface' => $event['surface'],
                'screen_name' => $event['screen_name'],
                'block_id' => $event['block_id'],
                'block_type' => $event['block_type'],
                'placement_id' => $event['placement_id'],
                'content_id' => $event['content_id'],
                'content_type' => $event['content_type'],
                'campaign_id' => $event['campaign_id'],
                'creative_id' => $event['creative_id'],
                'occurred_at' => CarbonImmutable::parse($event['occurred_at']),
                'event_date' => '2026-04-03',
                'properties' => $event['properties'],
            ]);
        }

        $this->artisan('insights:rollup-daily', ['--date' => '2026-04-03'])
            ->assertExitCode(0);

        $this->artisan('insights:rollup-hourly', ['--hour' => '2026-04-03 10:00:00'])
            ->assertExitCode(0);

        $this->assertDatabaseHas('agg_daily_campaign_metrics', [
            'campaign_id' => 'cmp_2026_001',
            'served_count' => 1,
            'qualified_impressions' => 1,
            'clicks' => 1,
        ]);

        $this->assertDatabaseHas('agg_hourly_service_metrics', [
            'service' => 'media',
            'content_id' => 'stream_1',
            'audio_starts' => 1,
            'audio_listen_seconds' => 30,
        ]);

        $this->assertDatabaseHas('agg_daily_surface_metrics', [
            'service' => 'media',
            'surface' => 'watch_page',
            'avg_time_spent_seconds' => 60,
        ]);

        $this->assertDatabaseHas('agg_daily_user_metrics', [
            'platform' => 'android',
            'avg_session_duration_seconds' => 1200,
        ]);

        $this->withHeader('X-API-Key', 'secret-token')
            ->getJson('/api/v1/reports/overview?date_from=2026-04-03&date_to=2026-04-03')
            ->assertOk()
            ->assertJsonPath('summary.screen_views', 2)
            ->assertJsonPath('summary.unique_users', 2)
            ->assertJsonPath('active_users.dau', 2)
            ->assertJsonPath('summary.sponsor_impressions', 1)
            ->assertJsonPath('summary.sponsor_clicks', 1);

        $this->withHeader('X-API-Key', 'secret-token')
            ->getJson('/api/v1/reports/overview?date_from=2026-04-03&date_to=2026-04-03&service=news')
            ->assertOk()
            ->assertJsonPath('summary.screen_views', 1)
            ->assertJsonPath('summary.unique_users', 2)
            ->assertJsonPath('active_users.dau', 2)
            ->assertJsonPath('top_surfaces.0.unique_users', 1)
            ->assertJsonPath('top_surfaces.1.unique_users', 1);

        $this->withHeader('X-API-Key', 'secret-token')
            ->getJson('/api/v1/reports/overview?date_from=2026-04-03&date_to=2026-04-03&service=news&surface=article_page')
            ->assertOk()
            ->assertJsonPath('summary.screen_views', 0)
            ->assertJsonPath('summary.unique_users', 1)
            ->assertJsonPath('active_users.dau', 1);

        $this->withHeader('X-API-Key', 'secret-token')
            ->getJson('/api/v1/reports/campaigns/cmp_2026_001?date_from=2026-04-03&date_to=2026-04-03')
            ->assertOk()
            ->assertJsonPath('summary.qualified_impressions', 1)
            ->assertJsonPath('summary.clicks', 1)
            ->assertJsonPath('by_placement.0.placement_id', 'match_center_header_companion');

        $this->withHeader('X-API-Key', 'secret-token')
            ->getJson('/api/v1/reports/content?date_from=2026-04-03&date_to=2026-04-03&content_type=article')
            ->assertOk()
            ->assertJsonPath('items.0.content_id', 'article_981');

        $this->withHeader('X-API-Key', 'secret-token')
            ->getJson('/api/v1/reports/live?date_from=2026-04-03&date_to=2026-04-03')
            ->assertOk()
            ->assertJsonPath('summary.audio_starts', 1)
            ->assertJsonPath('summary.listen_seconds_total', 30);
    }

    public function test_it_returns_a_report_not_found_code_for_unknown_campaign_reports(): void
    {
        config()->set('insights.api_key', 'secret-token');

        $this->withHeader('X-API-Key', 'secret-token')
            ->getJson('/api/v1/reports/campaigns/cmp_missing_001')
            ->assertNotFound()
            ->assertJsonPath('code', 'REPORT_NOT_FOUND');
    }
}
