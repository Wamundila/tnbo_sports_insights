<?php

namespace App\Services\Analytics;

use App\Models\AnalyticsEvent;
use App\Models\AnalyticsEventDedup;
use App\Models\AnalyticsSession;
use App\Models\Campaign;
use App\Models\CampaignCreative;
use App\Models\CampaignDeliveryLog;
use App\Models\Placement;
use App\Models\SponsorBlockEvent;
use App\Support\ReportingTime;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class EventIngestionService
{
    public function ingestBatch(array $payload): array
    {
        $storedCount = 0;

        foreach ($payload['events'] as $event) {
            $storedCount += $this->ingestEvent($event, (int) $payload['schema_version']) ? 1 : 0;
        }

        return [
            'accepted' => true,
            'received_count' => count($payload['events']),
            'stored_count' => $storedCount,
            'invalid_count' => 0,
            'errors' => [],
        ];
    }

    public function ingestEvent(array $event, int $schemaVersion = 1): bool
    {
        $event = $this->normalizeEvent($event);
        $occurredAt = ReportingTime::utcInstant($event['occurred_at']);

        return DB::transaction(function () use ($event, $schemaVersion, $occurredAt): bool {
            $dedup = AnalyticsEventDedup::query()->firstOrCreate(
                ['event_id' => $event['event_id']],
                ['first_seen_at' => $occurredAt]
            );

            if (! $dedup->wasRecentlyCreated) {
                return false;
            }

            $this->materializeSession($event, $occurredAt);

            AnalyticsEvent::query()->create([
                'event_id' => $event['event_id'],
                'schema_version' => $schemaVersion,
                'session_id' => $event['session_id'],
                'user_id' => $event['user_id'] ?? null,
                'anonymous_id' => $event['anonymous_id'] ?? null,
                'device_id' => $event['device_id'] ?? null,
                'platform' => $event['platform'],
                'app_version' => $event['app_version'],
                'event_name' => $event['event_name'],
                'event_category' => $this->resolveEventCategory($event['event_name']),
                'service' => $event['service'],
                'surface' => $event['surface'],
                'screen_name' => $event['screen_name'],
                'block_id' => $event['block_id'] ?? null,
                'block_type' => $event['block_type'] ?? null,
                'placement_id' => $event['placement_id'] ?? null,
                'position_index' => $event['position_index'] ?? null,
                'content_id' => $event['content_id'] ?? null,
                'content_type' => $event['content_type'] ?? null,
                'campaign_id' => $event['campaign_id'] ?? null,
                'creative_id' => $event['creative_id'] ?? null,
                'match_id' => $event['match_id'] ?? null,
                'competition_id' => $event['competition_id'] ?? null,
                'team_id' => $event['team_id'] ?? null,
                'occurred_at' => $occurredAt,
                'event_date' => ReportingTime::eventDate($occurredAt),
                'properties' => $event['properties'] ?? null,
            ]);

            $this->mirrorSponsorEvent($event, $occurredAt);

            return true;
        });
    }

    private function materializeSession(array $event, CarbonImmutable $occurredAt): void
    {
        $session = AnalyticsSession::query()->firstOrNew([
            'session_id' => $event['session_id'],
        ]);

        $startedAt = $session->exists && $session->started_at !== null
            ? CarbonImmutable::parse($session->started_at)->min($occurredAt)
            : $occurredAt;

        $endedAt = $session->exists && $session->ended_at !== null
            ? CarbonImmutable::parse($session->ended_at)->max($occurredAt)
            : $occurredAt;

        $session->fill([
            'user_id' => $event['user_id'] ?? $session->user_id,
            'anonymous_id' => $event['anonymous_id'] ?? $session->anonymous_id,
            'device_id' => $event['device_id'] ?? $session->device_id,
            'platform' => $event['platform'] ?? $session->platform,
            'app_version' => $event['app_version'] ?? $session->app_version,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'last_seen_at' => $endedAt,
        ]);

        $session->save();
    }

    private function mirrorSponsorEvent(array $event, CarbonImmutable $occurredAt): void
    {
        $eventName = $event['event_name'];

        if (! str_starts_with($eventName, 'sponsor_') && $eventName !== 'campaign_served') {
            return;
        }

        $placement = isset($event['placement_id'])
            ? Placement::query()->where('code', $event['placement_id'])->first()
            : null;

        $campaign = isset($event['campaign_id'])
            ? Campaign::query()->where('code', $event['campaign_id'])->first()
            : null;

        $creative = isset($event['creative_id'])
            ? CampaignCreative::query()->where('code', $event['creative_id'])->first()
            : null;

        $deliveryLog = CampaignDeliveryLog::query()
            ->when(isset($event['properties']['delivery_id']), function ($query) use ($event) {
                $query->where('delivery_id', Arr::get($event, 'properties.delivery_id'));
            })
            ->latest('served_at')
            ->first();

        SponsorBlockEvent::query()->create([
            'event_id' => $event['event_id'],
            'delivery_log_id' => $deliveryLog?->id,
            'campaign_id' => $campaign?->id,
            'campaign_code' => $event['campaign_id'] ?? null,
            'creative_id' => $creative?->id,
            'creative_code' => $event['creative_id'] ?? null,
            'placement_id' => $placement?->id,
            'placement_code' => $event['placement_id'] ?? null,
            'session_id' => $event['session_id'],
            'user_id' => $event['user_id'] ?? null,
            'anonymous_id' => $event['anonymous_id'] ?? null,
            'service' => $event['service'],
            'surface' => $event['surface'],
            'block_id' => $event['block_id'] ?? null,
            'block_type' => $event['block_type'] ?? null,
            'event_name' => $eventName,
            'occurred_at' => $occurredAt,
            'properties' => $event['properties'] ?? null,
        ]);
    }

    private function resolveEventCategory(string $eventName): string
    {
        return match (true) {
            str_starts_with($eventName, 'article_') => 'news',
            str_starts_with($eventName, 'audio_'), str_starts_with($eventName, 'video_'), str_starts_with($eventName, 'media_') => 'media',
            str_starts_with($eventName, 'match_'), str_ends_with($eventName, '_tab_view'), $eventName === 'fixture_open', $eventName === 'lineup_expand' => 'match_center',
            str_starts_with($eventName, 'game_'), str_starts_with($eventName, 'poll_'), str_starts_with($eventName, 'prediction_') => 'interactive',
            str_starts_with($eventName, 'sponsor_'), $eventName === 'campaign_served' => 'sponsors',
            in_array($eventName, ['app_open', 'session_start', 'session_end', 'screen_view', 'screen_exit', 'search', 'share', 'bookmark_add', 'bookmark_remove', 'block_view', 'block_expand', 'item_impression', 'item_click'], true) => 'core',
            default => 'custom',
        };
    }

    private function normalizeEvent(array $event): array
    {
        $properties = [];

        if (isset($event['metadata']) && is_array($event['metadata'])) {
            $properties = $event['metadata'];
        }

        if (isset($event['properties']) && is_array($event['properties'])) {
            $properties = array_replace($properties, $event['properties']);
        }

        foreach (['campaign_id', 'creative_id', 'placement_id', 'block_id', 'block_type'] as $contextField) {
            $event[$contextField] = $this->promoteEventContext($event, $properties, $contextField);
        }

        foreach (['content_id', 'match_id', 'competition_id', 'team_id', 'campaign_id', 'creative_id', 'placement_id', 'block_id', 'block_type'] as $identifierField) {
            if (array_key_exists($identifierField, $event) && $event[$identifierField] !== null) {
                $event[$identifierField] = (string) $event[$identifierField];
            }
        }

        $event['properties'] = $properties !== [] ? $properties : null;

        unset($event['metadata']);

        return $event;
    }

    private function promoteEventContext(array $event, array $properties, string $field): mixed
    {
        if (array_key_exists($field, $event) && filled($event[$field])) {
            return $event[$field];
        }

        $camelField = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $field))));

        return $properties[$field] ?? $properties[$camelField] ?? null;
    }
}
