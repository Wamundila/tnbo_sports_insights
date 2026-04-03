<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\AnalyticsEventDedup;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RawEventRetentionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_prunes_old_raw_events_and_dedup_rows_outside_the_retention_window(): void
    {
        CarbonImmutable::setTestNow('2026-04-10 09:00:00');
        config()->set('insights.raw_event_retention_days', 7);

        AnalyticsEvent::query()->create($this->eventPayload(
            eventId: 'evt_old',
            occurredAt: '2026-04-02 12:00:00',
            eventDate: '2026-04-02'
        ));

        AnalyticsEvent::query()->create($this->eventPayload(
            eventId: 'evt_keep',
            occurredAt: '2026-04-03 12:00:00',
            eventDate: '2026-04-03'
        ));

        AnalyticsEventDedup::query()->create([
            'event_id' => 'evt_old',
            'first_seen_at' => '2026-04-02 12:00:00',
        ]);

        AnalyticsEventDedup::query()->create([
            'event_id' => 'evt_keep',
            'first_seen_at' => '2026-04-03 12:00:00',
        ]);

        $this->artisan('insights:prune-raw-events')
            ->expectsOutput('Pruned raw events before 2026-04-03. Deleted 1 analytics events and 1 dedup rows.')
            ->assertSuccessful();

        $this->assertDatabaseMissing('analytics_events', ['event_id' => 'evt_old']);
        $this->assertDatabaseHas('analytics_events', ['event_id' => 'evt_keep']);
        $this->assertDatabaseMissing('analytics_event_dedup', ['event_id' => 'evt_old']);
        $this->assertDatabaseHas('analytics_event_dedup', ['event_id' => 'evt_keep']);

        CarbonImmutable::setTestNow();
    }

    private function eventPayload(string $eventId, string $occurredAt, string $eventDate): array
    {
        return [
            'event_id' => $eventId,
            'schema_version' => 1,
            'session_id' => 'sess_001',
            'anonymous_id' => 'anon_001',
            'platform' => 'android',
            'app_version' => '1.0.0',
            'event_name' => 'screen_view',
            'event_category' => 'core',
            'service' => 'news',
            'surface' => 'home_page',
            'screen_name' => 'NewsHomeScreen',
            'occurred_at' => $occurredAt,
            'event_date' => $eventDate,
        ];
    }
}
