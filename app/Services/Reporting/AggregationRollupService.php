<?php

namespace App\Services\Reporting;

use App\Models\AggDailyBlockMetric;
use App\Models\AggDailyCampaignMetric;
use App\Models\AggDailyContentMetric;
use App\Models\AggDailySurfaceMetric;
use App\Models\AggDailyUserMetric;
use App\Models\AggHourlyServiceMetric;
use App\Models\AnalyticsEvent;
use App\Models\AnalyticsSession;
use App\Models\Campaign;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class AggregationRollupService
{
    public function rollupDate(CarbonImmutable $date): void
    {
        $this->rollupDailySurfaceMetrics($date);
        $this->rollupDailyBlockMetrics($date);
        $this->rollupDailyContentMetrics($date);
        $this->rollupDailyCampaignMetrics($date);
        $this->rollupDailyUserMetrics($date);
    }

    public function rollupHour(CarbonImmutable $hour): void
    {
        $hourStart = $hour->startOfHour();
        $hourEnd = $hourStart->addHour();

        AggHourlyServiceMetric::query()
            ->where('metric_hour', $hourStart)
            ->delete();

        $events = AnalyticsEvent::query()
            ->where('occurred_at', '>=', $hourStart)
            ->where('occurred_at', '<', $hourEnd)
            ->get();

        $events
            ->groupBy(fn (AnalyticsEvent $event) => implode('|', [
                $event->service,
                $event->surface,
                $event->platform,
                $event->match_id,
                $event->content_id,
            ]))
            ->each(function (Collection $group) use ($hourStart): void {
                $first = $group->first();
                $identities = $this->distinctIdentities($group);

                AggHourlyServiceMetric::query()->create([
                    'metric_hour' => $hourStart,
                    'service' => $first->service,
                    'surface' => $first->surface,
                    'platform' => $first->platform,
                    'match_id' => $first->match_id,
                    'content_id' => $first->content_id,
                    'sessions' => $group->pluck('session_id')->filter()->unique()->count(),
                    'unique_users' => $identities->count(),
                    'screen_views' => $group->where('event_name', 'screen_view')->count(),
                    'content_opens' => $group->filter(fn (AnalyticsEvent $event) => $this->isContentOpenEvent($event->event_name))->count(),
                    'sponsor_impressions' => $group->filter(fn (AnalyticsEvent $event) => $this->isSponsorImpressionEvent($event->event_name))->count(),
                    'sponsor_clicks' => $group->filter(fn (AnalyticsEvent $event) => in_array($event->event_name, ['sponsor_click', 'sponsor_cta_click'], true))->count(),
                    'audio_starts' => $group->where('event_name', 'audio_play')->count(),
                    'audio_listen_seconds' => $group->sum(fn (AnalyticsEvent $event) => $event->event_name === 'audio_heartbeat'
                        ? (int) data_get($event->properties, 'heartbeat_interval_seconds', 0)
                        : 0),
                    'game_starts' => $group->where('event_name', 'game_start')->count(),
                    'poll_votes' => $group->where('event_name', 'poll_vote')->count(),
                ]);
            });
    }

    private function rollupDailySurfaceMetrics(CarbonImmutable $date): void
    {
        AggDailySurfaceMetric::query()->where('metric_date', $date->toDateString())->delete();

        $events = AnalyticsEvent::query()
            ->whereDate('event_date', $date->toDateString())
            ->get();

        $avgTimes = $events
            ->filter(fn (AnalyticsEvent $event) => filled($event->session_id))
            ->groupBy(fn (AnalyticsEvent $event) => implode('|', [$event->service, $event->surface, $event->platform, $event->session_id]))
            ->mapWithKeys(function (Collection $group, string $key): array {
                $timestamps = $group
                    ->pluck('occurred_at')
                    ->filter()
                    ->sort()
                    ->values();

                $duration = $timestamps->count() > 1
                    ? $this->secondsBetween($timestamps->first(), $timestamps->last())
                    : 0;

                return [$key => $duration];
            });

        $events
            ->groupBy(fn (AnalyticsEvent $event) => implode('|', [$event->service, $event->surface, $event->platform]))
            ->each(function (Collection $group, string $key) use ($date, $avgTimes): void {
                [$service, $surface, $platform] = explode('|', $key);
                $sessionDurations = $avgTimes->filter(fn ($value, $avgKey) => str_starts_with($avgKey, "{$service}|{$surface}|{$platform}|"));
                $mode = $group->pluck('screen_name')->filter()->mode();

                AggDailySurfaceMetric::query()->create([
                    'metric_date' => $date->toDateString(),
                    'service' => $service,
                    'surface' => $surface,
                    'platform' => $platform !== '' ? $platform : null,
                    'screen_name' => is_array($mode) ? ($mode[0] ?? null) : null,
                    'sessions' => $group->pluck('session_id')->filter()->unique()->count(),
                    'unique_users' => $this->distinctIdentities($group)->count(),
                    'screen_views' => $group->where('event_name', 'screen_view')->count(),
                    'exits' => $group->where('event_name', 'screen_exit')->count(),
                    'sponsor_impressions' => $group->filter(fn (AnalyticsEvent $event) => $this->isSponsorImpressionEvent($event->event_name))->count(),
                    'sponsor_clicks' => $group->filter(fn (AnalyticsEvent $event) => in_array($event->event_name, ['sponsor_click', 'sponsor_cta_click'], true))->count(),
                    'avg_time_spent_seconds' => $sessionDurations->isEmpty()
                        ? 0
                        : (int) round($sessionDurations->avg()),
                ]);
            });
    }

    private function rollupDailyBlockMetrics(CarbonImmutable $date): void
    {
        AggDailyBlockMetric::query()->where('metric_date', $date->toDateString())->delete();

        AnalyticsEvent::query()
            ->whereDate('event_date', $date->toDateString())
            ->whereNotNull('block_id')
            ->get()
            ->groupBy(fn (AnalyticsEvent $event) => implode('|', [
                $event->service,
                $event->surface,
                $event->block_id,
                $event->block_type,
                $event->placement_id,
            ]))
            ->each(function (Collection $group, string $key) use ($date): void {
                [$service, $surface, $blockId, $blockType, $placementId] = explode('|', $key);
                $blockViews = $group->filter(fn (AnalyticsEvent $event) => $event->event_name === 'block_view' || $this->isSponsorImpressionEvent($event->event_name))->count();
                $blockClicks = $group->filter(fn (AnalyticsEvent $event) => in_array($event->event_name, ['item_click', 'sponsor_click', 'sponsor_cta_click'], true))->count();
                $sponsorClicks = $group->filter(fn (AnalyticsEvent $event) => in_array($event->event_name, ['sponsor_click', 'sponsor_cta_click'], true))->count();
                $sponsorImpressions = $group->filter(fn (AnalyticsEvent $event) => $this->isSponsorImpressionEvent($event->event_name))->count();

                AggDailyBlockMetric::query()->create([
                    'metric_date' => $date->toDateString(),
                    'service' => $service,
                    'surface' => $surface,
                    'block_id' => $blockId,
                    'block_type' => $blockType !== '' ? $blockType : null,
                    'placement_id' => $placementId !== '' ? $placementId : null,
                    'block_views' => $blockViews,
                    'block_clicks' => $blockClicks,
                    'unique_viewers' => $this->distinctIdentities(
                        $group->filter(fn (AnalyticsEvent $event) => $event->event_name === 'block_view' || $this->isSponsorImpressionEvent($event->event_name))
                    )->count(),
                    'sponsor_impressions' => $sponsorImpressions,
                    'sponsor_clicks' => $sponsorClicks,
                    'ctr' => $sponsorImpressions > 0 ? round($sponsorClicks / $sponsorImpressions, 4) : 0,
                ]);
            });
    }

    private function rollupDailyContentMetrics(CarbonImmutable $date): void
    {
        AggDailyContentMetric::query()->where('metric_date', $date->toDateString())->delete();

        AnalyticsEvent::query()
            ->whereDate('event_date', $date->toDateString())
            ->whereNotNull('content_id')
            ->get()
            ->groupBy(fn (AnalyticsEvent $event) => implode('|', [
                $event->service,
                $event->content_type,
                $event->content_id,
            ]))
            ->each(function (Collection $group, string $key) use ($date): void {
                [$service, $contentType, $contentId] = explode('|', $key);

                AggDailyContentMetric::query()->create([
                    'metric_date' => $date->toDateString(),
                    'service' => $service,
                    'content_type' => $contentType !== '' ? $contentType : 'unknown',
                    'content_id' => $contentId,
                    'opens' => $group->filter(fn (AnalyticsEvent $event) => $this->isContentOpenEvent($event->event_name))->count(),
                    'unique_users' => $this->distinctIdentities($group)->count(),
                    'completions' => $group->filter(fn (AnalyticsEvent $event) => $this->isCompletionEvent($event->event_name))->count(),
                    'shares' => $group->filter(fn (AnalyticsEvent $event) => in_array($event->event_name, ['share', 'article_share'], true))->count(),
                    'avg_engagement_seconds' => (int) round($group
                        ->map(fn (AnalyticsEvent $event) => $this->engagementSeconds($event))
                        ->filter(fn (int $value) => $value > 0)
                        ->avg() ?? 0),
                ]);
            });
    }

    private function rollupDailyCampaignMetrics(CarbonImmutable $date): void
    {
        AggDailyCampaignMetric::query()->where('metric_date', $date->toDateString())->delete();

        $campaignSponsorCodes = Campaign::query()
            ->with('sponsor:id,code')
            ->get()
            ->mapWithKeys(fn (Campaign $campaign) => [$campaign->code => $campaign->sponsor?->code]);

        AnalyticsEvent::query()
            ->whereDate('event_date', $date->toDateString())
            ->whereNotNull('campaign_id')
            ->get()
            ->groupBy(fn (AnalyticsEvent $event) => implode('|', [
                $event->campaign_id,
                $event->creative_id,
                $event->placement_id,
                $event->service,
                $event->surface,
            ]))
            ->each(function (Collection $group, string $key) use ($date, $campaignSponsorCodes): void {
                [$campaignId, $creativeId, $placementId, $service, $surface] = explode('|', $key);
                $served = $group->where('event_name', 'campaign_served')->count();
                $rendered = $group->filter(fn (AnalyticsEvent $event) => $this->isSponsorRenderedEvent($event->event_name))->count();
                $qualified = $group->filter(fn (AnalyticsEvent $event) => $this->isSponsorImpressionEvent($event->event_name))->count();
                $clicks = $group->filter(fn (AnalyticsEvent $event) => in_array($event->event_name, ['sponsor_click', 'sponsor_cta_click'], true))->count();

                AggDailyCampaignMetric::query()->create([
                    'metric_date' => $date->toDateString(),
                    'sponsor_id' => $campaignSponsorCodes->get($campaignId),
                    'campaign_id' => $campaignId !== '' ? $campaignId : null,
                    'creative_id' => $creativeId !== '' ? $creativeId : null,
                    'placement_id' => $placementId !== '' ? $placementId : null,
                    'service' => $service !== '' ? $service : null,
                    'surface' => $surface !== '' ? $surface : null,
                    'served_count' => $served,
                    'rendered_count' => $rendered,
                    'qualified_impressions' => $qualified,
                    'unique_reach' => $this->distinctIdentities($group)->count(),
                    'clicks' => $clicks,
                    'completions' => $group->filter(fn (AnalyticsEvent $event) => $this->isCompletionEvent($event->event_name))->count(),
                    'ctr' => $qualified > 0 ? round($clicks / $qualified, 4) : 0,
                ]);
            });
    }

    private function rollupDailyUserMetrics(CarbonImmutable $date): void
    {
        AggDailyUserMetric::query()->where('metric_date', $date->toDateString())->delete();

        $events = AnalyticsEvent::query()
            ->whereDate('event_date', $date->toDateString())
            ->get()
            ->groupBy('platform');

        $firstSeenIdentityDates = AnalyticsEvent::query()
            ->selectRaw("COALESCE(NULLIF(user_id, ''), anonymous_id) as identity_key, MIN(event_date) as first_event_date")
            ->groupBy('identity_key')
            ->pluck('first_event_date', 'identity_key');

        $sessionDurations = AnalyticsSession::query()
            ->whereDate('started_at', $date->toDateString())
            ->get()
            ->groupBy('platform');

        $events->each(function (Collection $group, string $platform) use ($date, $firstSeenIdentityDates, $sessionDurations): void {
            $identities = $this->distinctIdentities($group);
            $newUsers = $identities->filter(fn (string $identity) => $firstSeenIdentityDates->get($identity) === $date->toDateString());
            $platformSessions = $sessionDurations->get($platform, collect());
            $avgDuration = $platformSessions->avg(function (AnalyticsSession $session): int {
                if (! $session->started_at || ! $session->ended_at) {
                    return 0;
                }

                return $this->secondsBetween($session->started_at, $session->ended_at);
            });

            AggDailyUserMetric::query()->create([
                'metric_date' => $date->toDateString(),
                'platform' => $platform !== '' ? $platform : null,
                'dau' => $identities->count(),
                'new_users' => $newUsers->count(),
                'returning_users' => $identities->count() - $newUsers->count(),
                'avg_sessions_per_user' => $identities->count() > 0
                    ? (int) round($group->pluck('session_id')->filter()->unique()->count() / $identities->count())
                    : 0,
                'avg_session_duration_seconds' => (int) round($avgDuration ?? 0),
            ]);
        });
    }

    private function distinctIdentities(Collection $events): Collection
    {
        return $events
            ->map(fn ($event) => $event->user_id ?: $event->anonymous_id)
            ->filter()
            ->unique()
            ->values();
    }

    private function isContentOpenEvent(string $eventName): bool
    {
        return in_array($eventName, [
            'article_open',
            'media_open',
            'video_play',
            'audio_play',
            'fixture_open',
            'match_center_open',
            'game_open',
            'game_start',
            'poll_view',
            'prediction_open',
        ], true);
    }

    private function isCompletionEvent(string $eventName): bool
    {
        return in_array($eventName, [
            'article_complete',
            'video_complete',
            'game_complete',
            'sponsor_video_complete',
        ], true);
    }

    private function isSponsorImpressionEvent(string $eventName): bool
    {
        return in_array($eventName, ['sponsor_block_view', 'sponsor_impression'], true);
    }

    private function isSponsorRenderedEvent(string $eventName): bool
    {
        return in_array($eventName, ['sponsor_block_rendered', 'sponsor_rendered'], true);
    }

    private function engagementSeconds(AnalyticsEvent $event): int
    {
        return (int) (
            data_get($event->properties, 'read_time_seconds')
            ?? data_get($event->properties, 'listen_seconds_total')
            ?? data_get($event->properties, 'current_position_seconds')
            ?? 0
        );
    }

    private function secondsBetween(mixed $start, mixed $end): int
    {
        $startAt = CarbonImmutable::parse($start);
        $endAt = CarbonImmutable::parse($end);

        return max(0, (int) $startAt->diffInSeconds($endAt));
    }
}
