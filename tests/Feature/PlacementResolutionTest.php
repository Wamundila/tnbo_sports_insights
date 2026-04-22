<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\Campaign;
use App\Models\CampaignCreative;
use App\Models\CampaignDeliveryLog;
use App\Models\CampaignTarget;
use App\Models\Placement;
use App\Models\Sponsor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PlacementResolutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resolves_an_active_campaign_for_a_requested_placement(): void
    {
        config()->set('insights.api_key', 'secret-token');

        $placement = Placement::query()->create([
            'code' => 'match_center_header_companion',
            'name' => 'Match Center Header Companion',
            'service' => 'match_center',
            'surface' => 'match_center_page',
            'block_type' => 'sponsor_card',
            'allowed_creative_types' => ['sponsor_card'],
            'is_active' => true,
        ]);

        $sponsor = Sponsor::query()->create([
            'code' => 'zamtel',
            'name' => 'Zamtel',
            'status' => 'active',
        ]);

        $campaign = Campaign::query()->create([
            'sponsor_id' => $sponsor->id,
            'code' => 'cmp_2026_001',
            'name' => 'Zamtel MatchDay Partner',
            'status' => 'active',
            'start_at' => Carbon::now()->subDay(),
            'end_at' => Carbon::now()->addDay(),
        ]);

        $creative = CampaignCreative::query()->create([
            'campaign_id' => $campaign->id,
            'code' => 'creative_01',
            'creative_type' => 'sponsor_card',
            'label_text' => 'Sponsored',
            'title' => 'Zamtel MatchDay Partner',
            'body' => 'Stay connected through every match day.',
            'image_url' => 'https://cdn.example.com/zamtel-card.jpg',
            'logo_url' => 'https://cdn.example.com/zamtel-logo.png',
            'cta_text' => 'Learn more',
            'cta_url' => 'https://example.com/zamtel',
            'status' => 'active',
        ]);

        CampaignTarget::query()->create([
            'campaign_id' => $campaign->id,
            'placement_id' => $placement->id,
            'service' => 'match_center',
            'surface' => 'match_center_page',
            'priority' => 100,
            'weight' => 1,
            'is_active' => true,
        ]);

        $payload = [
            'user_id' => 'ts_1',
            'anonymous_id' => 'anon_ab12',
            'session_id' => 'sess_001',
            'platform' => 'android',
            'service' => 'match_center',
            'surface' => 'match_center_page',
            'screen_name' => 'MatchDetailScreen',
            'context' => [
                'match_id' => 'match_5541',
                'competition_id' => 'super_league_2026',
            ],
            'placements' => [
                'match_center_header_companion',
            ],
        ];

        $response = $this
            ->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/placements/resolve', $payload);

        $response
            ->assertOk()
            ->assertJsonPath('placements.0.placement_id', 'match_center_header_companion')
            ->assertJsonPath('placements.0.served_event.event_name', 'campaign_served')
            ->assertJsonPath('placements.0.creative.campaign_id', 'cmp_2026_001')
            ->assertJsonPath('placements.0.creative.creative_id', 'creative_01')
            ->assertJsonPath('placements.0.creative.creative_type', 'sponsor_card');

        $this->assertDatabaseHas('campaign_delivery_logs', [
            'campaign_id' => $campaign->id,
            'creative_id' => $creative->id,
            'placement_id' => $placement->id,
            'placement_code' => 'match_center_header_companion',
            'service' => 'match_center',
            'surface' => 'match_center_page',
        ]);

        $deliveryLog = CampaignDeliveryLog::query()->first();

        $this->assertDatabaseHas('analytics_events', [
            'event_name' => 'campaign_served',
            'campaign_id' => 'cmp_2026_001',
            'creative_id' => 'creative_01',
            'placement_id' => 'match_center_header_companion',
        ]);

        $this->assertDatabaseHas('sponsor_block_events', [
            'event_name' => 'campaign_served',
            'delivery_log_id' => $deliveryLog->id,
            'campaign_code' => 'cmp_2026_001',
            'creative_code' => 'creative_01',
            'placement_code' => 'match_center_header_companion',
        ]);
    }

    public function test_it_derives_served_event_date_using_reporting_timezone(): void
    {
        config()->set('insights.api_key', 'secret-token');
        config()->set('insights.reporting_timezone', 'Africa/Lusaka');

        Carbon::setTestNow(Carbon::parse('2026-04-03T22:30:00Z'));

        $placement = Placement::query()->create([
            'code' => 'match_center_header_companion',
            'name' => 'Match Center Header Companion',
            'service' => 'match_center',
            'surface' => 'match_center_page',
            'block_type' => 'sponsor_card',
            'allowed_creative_types' => ['sponsor_card'],
            'is_active' => true,
        ]);

        $sponsor = Sponsor::query()->create([
            'code' => 'zamtel',
            'name' => 'Zamtel',
            'status' => 'active',
        ]);

        $campaign = Campaign::query()->create([
            'sponsor_id' => $sponsor->id,
            'code' => 'cmp_timezone_001',
            'name' => 'Timezone Campaign',
            'status' => 'active',
            'start_at' => Carbon::now()->subDay(),
            'end_at' => Carbon::now()->addDay(),
        ]);

        CampaignCreative::query()->create([
            'campaign_id' => $campaign->id,
            'code' => 'creative_timezone_01',
            'creative_type' => 'sponsor_card',
            'title' => 'Timezone Creative',
            'status' => 'active',
        ]);

        CampaignTarget::query()->create([
            'campaign_id' => $campaign->id,
            'placement_id' => $placement->id,
            'service' => 'match_center',
            'surface' => 'match_center_page',
            'priority' => 100,
            'weight' => 1,
            'is_active' => true,
        ]);

        $this->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/placements/resolve', [
                'anonymous_id' => 'anon_ab12',
                'session_id' => 'sess_001',
                'platform' => 'android',
                'service' => 'match_center',
                'surface' => 'match_center_page',
                'screen_name' => 'MatchDetailScreen',
                'placements' => [
                    'match_center_header_companion',
                ],
            ])
            ->assertOk()
            ->assertJsonPath('placements.0.placement_id', 'match_center_header_companion');

        $event = AnalyticsEvent::query()->where('event_name', 'campaign_served')->firstOrFail();

        $this->assertSame('2026-04-04', $event->event_date->toDateString());

        Carbon::setTestNow();
    }

    public function test_it_returns_only_successfully_resolved_placements_when_some_slots_have_no_campaign(): void
    {
        config()->set('insights.api_key', 'secret-token');

        $eligiblePlacement = Placement::query()->create([
            'code' => 'home_inline_1',
            'name' => 'Home Inline 1',
            'service' => 'news',
            'surface' => 'home_page',
            'block_type' => 'sponsor_card',
            'allowed_creative_types' => ['sponsor_card'],
            'is_active' => true,
        ]);

        Placement::query()->create([
            'code' => 'home_inline_2',
            'name' => 'Home Inline 2',
            'service' => 'news',
            'surface' => 'home_page',
            'block_type' => 'sponsor_card',
            'allowed_creative_types' => ['sponsor_card'],
            'is_active' => true,
        ]);

        $sponsor = Sponsor::query()->create([
            'code' => 'partner_one',
            'name' => 'Partner One',
            'status' => 'active',
        ]);

        $campaign = Campaign::query()->create([
            'sponsor_id' => $sponsor->id,
            'code' => 'cmp_2026_home_001',
            'name' => 'Home Sponsor',
            'status' => 'active',
            'start_at' => Carbon::now()->subDay(),
            'end_at' => Carbon::now()->addDay(),
        ]);

        CampaignCreative::query()->create([
            'campaign_id' => $campaign->id,
            'code' => 'creative_home_01',
            'creative_type' => 'sponsor_card',
            'label_text' => 'Sponsored',
            'title' => 'Partner Message',
            'body' => 'Example body',
            'image_url' => 'https://cdn.example.com/image.jpg',
            'cta_text' => 'Learn more',
            'cta_url' => 'https://example.com',
            'status' => 'active',
        ]);

        CampaignTarget::query()->create([
            'campaign_id' => $campaign->id,
            'placement_id' => $eligiblePlacement->id,
            'service' => 'news',
            'surface' => 'home_page',
            'priority' => 50,
            'weight' => 1,
            'is_active' => true,
        ]);

        $response = $this
            ->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/placements/resolve', [
                'user_id' => 'ts_1',
                'anonymous_id' => 'anon_ab12',
                'session_id' => 'sess_001',
                'platform' => 'android',
                'service' => 'news',
                'surface' => 'home_page',
                'screen_name' => 'HomeScreen',
                'placements' => [
                    'home_inline_1',
                    'home_inline_2',
                ],
            ]);

        $response
            ->assertOk()
            ->assertJsonCount(1, 'placements')
            ->assertJsonPath('placements.0.placement_id', 'home_inline_1');
    }
}
