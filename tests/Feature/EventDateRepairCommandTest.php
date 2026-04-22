<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventDateRepairCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_repairs_event_dates_using_reporting_timezone(): void
    {
        config()->set('insights.reporting_timezone', 'Africa/Lusaka');

        AnalyticsEvent::query()->create([
            'event_id' => 'evt_repair_timezone_001',
            'schema_version' => 1,
            'session_id' => 'sess_001',
            'anonymous_id' => 'anon_1',
            'platform' => 'android',
            'app_version' => '1.0.0',
            'event_name' => 'screen_view',
            'event_category' => 'core',
            'service' => 'news',
            'surface' => 'home_page',
            'screen_name' => 'NewsHomeScreen',
            'occurred_at' => '2026-04-03 22:30:00',
            'event_date' => '2026-04-03',
        ]);

        $this->artisan('insights:repair-event-dates', [
            '--from-date' => '2026-04-03',
            '--to-date' => '2026-04-03',
        ])
            ->expectsOutput('Scanned 1 analytics events. Repaired 1 event_date values.')
            ->assertExitCode(0);

        $event = AnalyticsEvent::query()->where('event_id', 'evt_repair_timezone_001')->firstOrFail();

        $this->assertSame('2026-04-04', $event->event_date->toDateString());
    }
}
