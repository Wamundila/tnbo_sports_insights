<?php

namespace Tests\Feature;

use App\Models\AggDailyCampaignMetric;
use App\Models\AggDailySurfaceMetric;
use App\Models\Campaign;
use App\Models\CampaignCreative;
use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_and_admin_can_sign_in(): void
    {
        $user = User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('TNBO Insights Admin');

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_admin_can_view_dashboard_and_htmx_overview_report(): void
    {
        $user = User::factory()->create();

        AggDailySurfaceMetric::query()->create([
            'metric_date' => '2026-04-03',
            'service' => 'news',
            'surface' => 'home_page',
            'platform' => 'android',
            'screen_name' => 'NewsHomeScreen',
            'sessions' => 10,
            'unique_users' => 8,
            'screen_views' => 16,
            'exits' => 4,
            'sponsor_impressions' => 5,
            'sponsor_clicks' => 1,
            'avg_time_spent_seconds' => 90,
        ]);

        AggDailyCampaignMetric::query()->create([
            'metric_date' => '2026-04-03',
            'campaign_id' => 'cmp_2026_001',
            'placement_id' => 'home_inline_1',
            'service' => 'news',
            'surface' => 'home_page',
            'served_count' => 5,
            'rendered_count' => 5,
            'qualified_impressions' => 5,
            'unique_reach' => 4,
            'clicks' => 1,
            'completions' => 0,
            'ctr' => 0.2,
        ]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Operational Dashboard')
            ->assertSee('16');

        $this->actingAs($user)
            ->withHeader('HX-Request', 'true')
            ->get('/admin/reports/overview?date_from=2026-04-03&date_to=2026-04-03')
            ->assertOk()
            ->assertSee('Top Surfaces')
            ->assertDontSee('<!DOCTYPE html>', false);
    }

    public function test_authenticated_admin_can_view_getting_started_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.getting-started'))
            ->assertOk()
            ->assertSee('Getting Started')
            ->assertSee('Recommended Workflow')
            ->assertSee('Reporting Terms');
    }

    public function test_authenticated_admin_can_create_sponsor_from_web_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/admin/sponsors', [
                'code' => 'zamtel',
                'name' => 'Zamtel',
                'status' => 'active',
                'website_url' => 'https://example.com',
            ])
            ->assertRedirect(route('admin.sponsors.index'));

        $this->assertDatabaseHas('sponsors', [
            'code' => 'zamtel',
            'name' => 'Zamtel',
        ]);
    }

    public function test_authenticated_admin_can_open_and_update_a_sponsor(): void
    {
        $user = User::factory()->create();
        $sponsor = Sponsor::query()->create([
            'code' => 'zamtel',
            'name' => 'Zamtel',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('admin.sponsors.edit', $sponsor))
            ->assertOk()
            ->assertSee('Edit Sponsor')
            ->assertSee('zamtel');

        $this->actingAs($user)
            ->put(route('admin.sponsors.update', $sponsor), [
                'code' => 'zamtel',
                'name' => 'Zamtel Updated',
                'status' => 'inactive',
                'website_url' => 'https://example.com',
            ])
            ->assertRedirect(route('admin.sponsors.edit', $sponsor));

        $this->assertDatabaseHas('sponsors', [
            'id' => $sponsor->id,
            'name' => 'Zamtel Updated',
            'status' => 'inactive',
        ]);
    }

    public function test_authenticated_admin_can_update_a_campaign_from_the_admin_view(): void
    {
        $user = User::factory()->create();
        $sponsor = Sponsor::query()->create([
            'code' => 'partner_one',
            'name' => 'Partner One',
            'status' => 'active',
        ]);
        $campaign = Campaign::query()->create([
            'sponsor_id' => $sponsor->id,
            'code' => 'cmp_2026_001',
            'name' => 'Original Campaign',
            'status' => 'draft',
            'priority' => 0,
        ]);

        $this->actingAs($user)
            ->put(route('admin.campaigns.update', $campaign), [
                'sponsor_id' => $sponsor->id,
                'code' => 'cmp_2026_001',
                'name' => 'Original Campaign',
                'objective' => 'awareness',
                'status' => 'active',
                'priority' => 25,
                'budget_notes' => 'Updated by admin test',
                'targeting_json' => '{"competition_id":["super_league_2026"]}',
                'frequency_cap_json' => '{"per_user_per_day":3}',
                'reporting_label' => 'Launch Package',
            ])
            ->assertRedirect(route('admin.campaigns.edit', $campaign));

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'status' => 'active',
            'priority' => 25,
            'reporting_label' => 'Launch Package',
        ]);
    }

    public function test_authenticated_admin_can_upload_creative_image_and_logo_files(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $sponsor = Sponsor::query()->create([
            'code' => 'partner_two',
            'name' => 'Partner Two',
            'status' => 'active',
        ]);
        $campaign = Campaign::query()->create([
            'sponsor_id' => $sponsor->id,
            'code' => 'cmp_2026_002',
            'name' => 'Upload Campaign',
            'status' => 'active',
            'priority' => 10,
        ]);

        $this->actingAs($user)
            ->post(route('admin.creatives.store'), [
                'campaign_id' => $campaign->id,
                'code' => 'creative_upload_01',
                'creative_type' => 'image_banner',
                'title' => 'Creative Upload',
                'image_file' => $this->fakePngUpload('hero.png'),
                'logo_file' => $this->fakePngUpload('logo.png'),
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.creatives.index'));

        /** @var CampaignCreative $creative */
        $creative = CampaignCreative::query()->where('code', 'creative_upload_01')->firstOrFail();

        $this->assertNotNull($creative->image_url);
        $this->assertNotNull($creative->logo_url);
        $this->assertStringStartsWith('/storage/creative-assets/images/', $creative->image_url);
        $this->assertStringStartsWith('/storage/creative-assets/logos/', $creative->logo_url);

        Storage::disk('public')->assertExists(str_replace('/storage/', '', $creative->image_url));
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $creative->logo_url));
    }

    private function fakePngUpload(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'png_');

        file_put_contents(
            $path,
            base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wn8j5QAAAAASUVORK5CYII=',
                true
            )
        );

        return new UploadedFile($path, $name, 'image/png', null, true);
    }
}
