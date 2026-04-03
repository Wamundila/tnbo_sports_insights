<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\AnalyticsEventDedup;
use App\Models\AnalyticsSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventBatchIngestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_a_valid_event_batch_and_materializes_the_session(): void
    {
        config()->set('insights.api_key', 'secret-token');

        $payload = [
            'source' => 'flutter_app',
            'schema_version' => 1,
            'events' => [
                [
                    'event_id' => 'evt_001',
                    'event_name' => 'screen_view',
                    'occurred_at' => '2026-04-03T10:10:00Z',
                    'service' => 'news',
                    'surface' => 'home_page',
                    'screen_name' => 'NewsHomeScreen',
                    'user_id' => 'ts_1',
                    'anonymous_id' => 'anon_1',
                    'session_id' => 'sess_001',
                    'platform' => 'android',
                    'app_version' => '1.0.0',
                    'properties' => [
                        'entry_point' => 'app_launch',
                    ],
                ],
            ],
        ];

        $response = $this
            ->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/events/batch', $payload);

        $response
            ->assertOk()
            ->assertJson([
                'accepted' => true,
                'received_count' => 1,
                'stored_count' => 1,
                'invalid_count' => 0,
            ]);

        $this->assertDatabaseHas('analytics_events', [
            'event_id' => 'evt_001',
            'event_name' => 'screen_view',
            'service' => 'news',
            'surface' => 'home_page',
            'user_id' => 'ts_1',
        ]);

        $this->assertDatabaseHas('analytics_sessions', [
            'session_id' => 'sess_001',
            'user_id' => 'ts_1',
            'anonymous_id' => 'anon_1',
        ]);

        $this->assertSame(1, AnalyticsEvent::count());
        $this->assertSame(1, AnalyticsEventDedup::count());
        $this->assertSame(1, AnalyticsSession::count());
    }

    public function test_it_deduplicates_replayed_events(): void
    {
        config()->set('insights.api_key', 'secret-token');

        $payload = [
            'source' => 'flutter_app',
            'schema_version' => 1,
            'events' => [
                [
                    'event_id' => 'evt_dup_001',
                    'event_name' => 'article_open',
                    'occurred_at' => '2026-04-03T10:15:00Z',
                    'service' => 'news',
                    'surface' => 'article_page',
                    'screen_name' => 'ArticleDetailScreen',
                    'anonymous_id' => 'anon_1',
                    'session_id' => 'sess_001',
                    'platform' => 'android',
                    'app_version' => '1.0.0',
                    'content_id' => 'article_981',
                    'content_type' => 'article',
                    'properties' => [],
                ],
            ],
        ];

        $this->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/events/batch', $payload)
            ->assertOk()
            ->assertJson(['stored_count' => 1]);

        $this->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/events/batch', $payload)
            ->assertOk()
            ->assertJson(['stored_count' => 0]);

        $this->assertSame(1, AnalyticsEvent::count());
        $this->assertSame(1, AnalyticsEventDedup::count());
    }

    public function test_it_rejects_unknown_event_fields(): void
    {
        config()->set('insights.api_key', 'secret-token');

        $payload = [
            'source' => 'flutter_app',
            'schema_version' => 1,
            'events' => [
                [
                    'event_id' => 'evt_invalid_001',
                    'event_name' => 'screen_view',
                    'occurred_at' => '2026-04-03T10:10:00Z',
                    'service' => 'news',
                    'surface' => 'home_page',
                    'screen_name' => 'NewsHomeScreen',
                    'anonymous_id' => 'anon_1',
                    'session_id' => 'sess_001',
                    'platform' => 'android',
                    'app_version' => '1.0.0',
                    'unexpected_field' => 'nope',
                ],
            ],
        ];

        $this->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/events/batch', $payload)
            ->assertStatus(422)
            ->assertJsonPath('code', 'EVENT_BATCH_INVALID')
            ->assertJsonValidationErrors(['events.0.unexpected_field']);
    }

    public function test_it_requires_the_api_key_when_configured(): void
    {
        config()->set('insights.api_key', 'secret-token');

        $payload = [
            'source' => 'flutter_app',
            'schema_version' => 1,
            'events' => [
                [
                    'event_id' => 'evt_auth_001',
                    'event_name' => 'screen_view',
                    'occurred_at' => '2026-04-03T10:10:00Z',
                    'service' => 'news',
                    'surface' => 'home_page',
                    'screen_name' => 'NewsHomeScreen',
                    'anonymous_id' => 'anon_1',
                    'session_id' => 'sess_001',
                    'platform' => 'android',
                    'app_version' => '1.0.0',
                ],
            ],
        ];

        $this->postJson('/api/v1/events/batch', $payload)
            ->assertUnauthorized()
            ->assertJsonPath('code', 'UNAUTHORIZED');
    }

    public function test_it_returns_a_specific_code_when_the_event_batch_is_too_large(): void
    {
        config()->set('insights.api_key', 'secret-token');

        $event = [
            'event_id' => 'evt_template',
            'event_name' => 'screen_view',
            'occurred_at' => '2026-04-03T10:10:00Z',
            'service' => 'news',
            'surface' => 'home_page',
            'screen_name' => 'NewsHomeScreen',
            'anonymous_id' => 'anon_1',
            'session_id' => 'sess_001',
            'platform' => 'android',
            'app_version' => '1.0.0',
        ];

        $events = [];

        for ($i = 0; $i < 1001; $i++) {
            $events[] = array_merge($event, [
                'event_id' => 'evt_'.$i,
            ]);
        }

        $this->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/events/batch', [
                'source' => 'flutter_app',
                'schema_version' => 1,
                'events' => $events,
            ])
            ->assertStatus(422)
            ->assertJsonPath('code', 'EVENT_BATCH_TOO_LARGE');
    }

    public function test_it_accepts_flutter_metadata_payloads_and_normalizes_numeric_identifiers(): void
    {
        config()->set('insights.api_key', 'secret-token');

        $payload = [
            'schema_version' => 1,
            'events' => [
                [
                    'event_id' => 'evt_b87ec3dccb8819d6',
                    'event_name' => 'match_open',
                    'occurred_at' => '2026-04-03T15:34:06.544558Z',
                    'service' => 'match_center',
                    'surface' => 'football_tournament_page',
                    'screen_name' => 'FootballTournamentPageScreen',
                    'anonymous_id' => 'anon_7e5058a24bcb1389',
                    'session_id' => 'sess_ebdcd8ef2d538631',
                    'platform' => 'android',
                    'app_version' => '1.0.0+1',
                    'block_id' => 'tournament_fixtures',
                    'block_type' => 'football_tournament_fixtures',
                    'content_type' => 'football_fixture',
                    'match_id' => 196,
                    'competition_id' => 3,
                    'metadata' => [
                        'match_id' => 196,
                        'competition_id' => 3,
                        'competition_slug' => 'tnbo-league',
                        'content_type' => 'football_fixture',
                        'block_id' => 'tournament_fixtures',
                        'block_type' => 'football_tournament_fixtures',
                        'content_presentation_type' => 'vertical_card_stack',
                    ],
                ],
            ],
        ];

        $response = $this
            ->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/events/batch', $payload);

        $response
            ->assertOk()
            ->assertJson([
                'accepted' => true,
                'received_count' => 1,
                'stored_count' => 1,
                'invalid_count' => 0,
            ]);

        $event = AnalyticsEvent::query()->where('event_id', 'evt_b87ec3dccb8819d6')->firstOrFail();

        $this->assertSame('196', $event->match_id);
        $this->assertSame('3', $event->competition_id);
        $this->assertSame('tnbo-league', $event->properties['competition_slug']);
        $this->assertSame('vertical_card_stack', $event->properties['content_presentation_type']);
    }
}
