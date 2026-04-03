<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_admin_managed_sponsor_inventory_records(): void
    {
        config()->set('insights.api_key', 'secret-token');

        $sponsorResponse = $this
            ->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/admin/sponsors', [
                'code' => 'zamtel',
                'name' => 'Zamtel',
                'status' => 'active',
                'website_url' => 'https://example.com',
            ]);

        $sponsorResponse->assertCreated();
        $sponsorId = $sponsorResponse->json('data.id');

        $placementResponse = $this
            ->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/admin/placements', [
                'code' => 'match_center_header_companion',
                'name' => 'Match Center Header Companion',
                'service' => 'match_center',
                'surface' => 'match_center_page',
                'block_type' => 'sponsor_card',
                'allowed_creative_types' => ['image_banner'],
                'is_active' => true,
            ]);

        $placementResponse->assertCreated();
        $placementId = $placementResponse->json('data.id');

        $campaignResponse = $this
            ->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/admin/campaigns', [
                'sponsor_id' => $sponsorId,
                'code' => 'cmp_2026_001',
                'name' => 'Zamtel MatchDay Partner',
                'status' => 'active',
                'start_at' => '2026-04-01T00:00:00Z',
                'end_at' => '2026-04-30T23:59:59Z',
            ]);

        $campaignResponse->assertCreated();
        $campaignId = $campaignResponse->json('data.id');

        $creativeResponse = $this
            ->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/admin/creatives', [
                'campaign_id' => $campaignId,
                'code' => 'creative_01',
                'creative_type' => 'image_banner',
                'label_text' => 'Sponsored',
                'title' => 'MatchDay Partner',
                'cta_url' => 'https://example.com/cta',
                'status' => 'active',
            ]);

        $creativeResponse->assertCreated();

        $targetResponse = $this
            ->withHeader('X-API-Key', 'secret-token')
            ->postJson('/api/v1/admin/targets', [
                'campaign_id' => $campaignId,
                'placement_id' => $placementId,
                'service' => 'match_center',
                'surface' => 'match_center_page',
                'priority' => 100,
                'weight' => 1,
                'is_active' => true,
            ]);

        $targetResponse->assertCreated();

        $this->assertDatabaseHas('sponsors', ['code' => 'zamtel']);
        $this->assertDatabaseHas('placements', ['code' => 'match_center_header_companion']);
        $this->assertDatabaseHas('campaigns', ['code' => 'cmp_2026_001']);
        $this->assertDatabaseHas('campaign_creatives', ['code' => 'creative_01']);
        $this->assertDatabaseHas('campaign_targets', [
            'campaign_id' => $campaignId,
            'placement_id' => $placementId,
        ]);
    }
}
