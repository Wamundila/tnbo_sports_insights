<?php

namespace App\Services\Sponsors;

use App\Models\AnalyticsEvent;
use App\Models\AnalyticsEventDedup;
use App\Models\CampaignCreative;
use App\Models\CampaignDeliveryLog;
use App\Models\CampaignTarget;
use App\Models\Placement;
use App\Models\SponsorBlockEvent;
use App\Support\ReportingTime;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlacementResolutionService
{
    public function resolve(array $payload): array
    {
        $placements = Placement::query()
            ->whereIn('code', $payload['placements'])
            ->where('service', $payload['service'])
            ->where('surface', $payload['surface'])
            ->where('is_active', true)
            ->get()
            ->keyBy('code');

        return collect($payload['placements'])
            ->map(function (string $placementCode) use ($payload, $placements): ?array {
                $placement = $placements->get($placementCode);

                if (! $placement) {
                    return null;
                }

                $target = $this->findTargetForPlacement($placement, $payload);

                if (! $target) {
                    return null;
                }

                return $this->buildPlacementResponse($placement, $target, $payload);
            })
            ->filter()
            ->values()
            ->all();
    }

    private function findTargetForPlacement(Placement $placement, array $payload): ?CampaignTarget
    {
        $now = CarbonImmutable::now();

        return CampaignTarget::query()
            ->with(['campaign.sponsor', 'campaign.creatives' => function ($query) {
                $query->where('status', 'active')->orderByDesc('id');
            }])
            ->where('placement_id', $placement->id)
            ->where('is_active', true)
            ->where(function ($query) use ($payload) {
                $query->whereNull('service')->orWhere('service', $payload['service']);
            })
            ->where(function ($query) use ($payload) {
                $query->whereNull('surface')->orWhere('surface', $payload['surface']);
            })
            ->whereHas('campaign', function ($query) use ($now) {
                $query->where('status', 'active')
                    ->where(function ($inner) use ($now) {
                        $inner->whereNull('start_at')->orWhere('start_at', '<=', $now);
                    })
                    ->where(function ($inner) use ($now) {
                        $inner->whereNull('end_at')->orWhere('end_at', '>=', $now);
                    });
            })
            ->orderByDesc('priority')
            ->orderByDesc('weight')
            ->first();
    }

    private function buildPlacementResponse(Placement $placement, CampaignTarget $target, array $payload): array
    {
        /** @var CampaignCreative|null $creative */
        $creative = $target->campaign->creatives->first();

        if (! $creative) {
            return [];
        }

        return DB::transaction(function () use ($placement, $target, $creative, $payload): array {
            $deliveryId = (string) Str::uuid();
            $servedAt = CarbonImmutable::now('UTC');

            $deliveryLog = CampaignDeliveryLog::query()->create([
                'delivery_id' => $deliveryId,
                'sponsor_id' => $target->campaign->sponsor_id,
                'campaign_id' => $target->campaign_id,
                'campaign_code' => $target->campaign->code,
                'creative_id' => $creative->id,
                'creative_code' => $creative->code,
                'placement_id' => $placement->id,
                'placement_code' => $placement->code,
                'session_id' => $payload['session_id'],
                'user_id' => $payload['user_id'] ?? null,
                'anonymous_id' => $payload['anonymous_id'] ?? null,
                'service' => $payload['service'],
                'surface' => $payload['surface'],
                'block_id' => $placement->code,
                'block_type' => $placement->block_type,
                'content_id' => data_get($payload, 'context.content_id'),
                'match_id' => data_get($payload, 'context.match_id'),
                'competition_id' => data_get($payload, 'context.competition_id'),
                'served_at' => $servedAt,
                'response_context' => [
                    'screen_name' => $payload['screen_name'],
                    'platform' => $payload['platform'],
                    'context' => $payload['context'] ?? [],
                ],
            ]);

            $servedEventId = sprintf('served_%s', $deliveryId);

            AnalyticsEventDedup::query()->create([
                'event_id' => $servedEventId,
                'first_seen_at' => $servedAt,
            ]);

            AnalyticsEvent::query()->create([
                'event_id' => $servedEventId,
                'schema_version' => 1,
                'session_id' => $payload['session_id'],
                'user_id' => $payload['user_id'] ?? null,
                'anonymous_id' => $payload['anonymous_id'] ?? null,
                'platform' => $payload['platform'],
                'app_version' => data_get($payload, 'context.app_version'),
                'event_name' => 'campaign_served',
                'event_category' => 'sponsors',
                'service' => $payload['service'],
                'surface' => $payload['surface'],
                'screen_name' => $payload['screen_name'],
                'block_id' => $placement->code,
                'block_type' => $placement->block_type,
                'placement_id' => $placement->code,
                'campaign_id' => $target->campaign->code,
                'creative_id' => $creative->code,
                'content_id' => data_get($payload, 'context.content_id'),
                'content_type' => data_get($payload, 'context.content_type'),
                'match_id' => data_get($payload, 'context.match_id'),
                'competition_id' => data_get($payload, 'context.competition_id'),
                'team_id' => data_get($payload, 'context.team_id'),
                'occurred_at' => $servedAt,
                'event_date' => ReportingTime::eventDate($servedAt),
                'properties' => [
                    'delivery_id' => $deliveryId,
                    'served_by' => 'insights',
                ],
            ]);

            SponsorBlockEvent::query()->create([
                'event_id' => $servedEventId,
                'delivery_log_id' => $deliveryLog->id,
                'campaign_id' => $target->campaign_id,
                'campaign_code' => $target->campaign->code,
                'creative_id' => $creative->id,
                'creative_code' => $creative->code,
                'placement_id' => $placement->id,
                'placement_code' => $placement->code,
                'session_id' => $payload['session_id'],
                'user_id' => $payload['user_id'] ?? null,
                'anonymous_id' => $payload['anonymous_id'] ?? null,
                'service' => $payload['service'],
                'surface' => $payload['surface'],
                'block_id' => $placement->code,
                'block_type' => $placement->block_type,
                'event_name' => 'campaign_served',
                'occurred_at' => $servedAt,
                'properties' => [
                    'delivery_id' => $deliveryId,
                    'screen_name' => $payload['screen_name'],
                ],
            ]);

            return [
                'placement_id' => $placement->code,
                'block_type' => $placement->block_type,
                'served_event' => [
                    'event_name' => 'campaign_served',
                    'event_id' => $servedEventId,
                    'campaign_id' => $target->campaign->code,
                    'creative_id' => $creative->code,
                    'delivery_id' => $deliveryId,
                ],
                'creative' => [
                    'campaign_id' => $target->campaign->code,
                    'creative_id' => $creative->code,
                    'creative_type' => $creative->creative_type,
                    'label_text' => $creative->label_text,
                    'title' => $creative->title,
                    'body' => $creative->body,
                    'image_url' => $creative->image_url,
                    'logo_url' => $creative->logo_url,
                    'cta_text' => $creative->cta_text,
                    'cta_url' => $creative->cta_url,
                    'metadata' => $creative->metadata_json,
                ],
            ];
        });
    }
}
