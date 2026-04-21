<?php

namespace App\Services\Reporting;

use App\Models\AggDailyBlockMetric;
use App\Models\AggDailyCampaignMetric;
use App\Models\AggDailyContentMetric;
use App\Models\AggDailySurfaceMetric;
use App\Models\AggHourlyServiceMetric;
use App\Models\AnalyticsEvent;
use App\Models\Campaign;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReportService
{
    public function overview(array $filters): array
    {
        [$from, $to] = $this->resolveRange($filters);

        $surfaceQuery = AggDailySurfaceMetric::query()
            ->whereDate('metric_date', '>=', $from->toDateString())
            ->whereDate('metric_date', '<=', $to->toDateString());

        $campaignQuery = AggDailyCampaignMetric::query()
            ->whereDate('metric_date', '>=', $from->toDateString())
            ->whereDate('metric_date', '<=', $to->toDateString());

        $blockQuery = AggDailyBlockMetric::query()
            ->whereDate('metric_date', '>=', $from->toDateString())
            ->whereDate('metric_date', '<=', $to->toDateString());

        if ($service = data_get($filters, 'service')) {
            $surfaceQuery->where('service', $service);
            $campaignQuery->where('service', $service);
            $blockQuery->where('service', $service);
        }

        if ($surface = data_get($filters, 'surface')) {
            $surfaceQuery->where('surface', $surface);
            $campaignQuery->where('surface', $surface);
            $blockQuery->where('surface', $surface);
        }

        if ($campaignId = data_get($filters, 'campaign_id')) {
            $campaignQuery->where('campaign_id', $campaignId);
        }

        return [
            'date_range' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'summary' => [
                'screen_views' => (int) $surfaceQuery->sum('screen_views'),
                'sessions' => (int) $surfaceQuery->sum('sessions'),
                'unique_users' => $this->uniqueUsersInRange($from, $to, $filters),
                'sponsor_impressions' => (int) $campaignQuery->sum('qualified_impressions'),
                'sponsor_clicks' => (int) $campaignQuery->sum('clicks'),
            ],
            'active_users' => [
                'dau' => $this->uniqueUsersInRange($to, $to, $filters),
                'wau' => $this->uniqueUsersInRange($to->subDays(6), $to, $filters),
                'mau' => $this->uniqueUsersInRange($to->subDays(29), $to, $filters),
            ],
            'top_surfaces' => (clone $surfaceQuery)
                ->select('service', 'surface')
                ->selectRaw('SUM(screen_views) as screen_views')
                ->selectRaw('SUM(unique_users) as unique_users')
                ->selectRaw('SUM(avg_time_spent_seconds) as avg_time_spent_seconds')
                ->groupBy('service', 'surface')
                ->orderByDesc('screen_views')
                ->limit(10)
                ->get()
                ->map(function ($surface) use ($from, $to) {
                    $surface->unique_users = $this->uniqueUsersInRange($from, $to, [
                        'service' => $surface->service,
                        'surface' => $surface->surface,
                    ]);

                    return $surface;
                }),
            'top_blocks' => (clone $blockQuery)
                ->select('service', 'surface', 'block_id', 'placement_id')
                ->selectRaw('SUM(block_views) as block_views')
                ->selectRaw('SUM(block_clicks) as block_clicks')
                ->selectRaw('SUM(sponsor_impressions) as sponsor_impressions')
                ->selectRaw('SUM(sponsor_clicks) as sponsor_clicks')
                ->groupBy('service', 'surface', 'block_id', 'placement_id')
                ->orderByDesc('block_views')
                ->limit(10)
                ->get(),
            'top_campaigns' => (clone $campaignQuery)
                ->select('campaign_id')
                ->selectRaw('SUM(qualified_impressions) as qualified_impressions')
                ->selectRaw('SUM(clicks) as clicks')
                ->selectRaw('SUM(unique_reach) as unique_reach')
                ->groupBy('campaign_id')
                ->orderByDesc('qualified_impressions')
                ->limit(10)
                ->get(),
        ];
    }

    public function campaign(string $campaignCode, array $filters): array
    {
        [$from, $to] = $this->resolveRange($filters);

        $campaign = Campaign::query()
            ->with('sponsor')
            ->where('code', $campaignCode)
            ->first();

        if (! $campaign) {
            throw new NotFoundHttpException('Report not found.');
        }

        $query = AggDailyCampaignMetric::query()
            ->where('campaign_id', $campaignCode)
            ->whereDate('metric_date', '>=', $from->toDateString())
            ->whereDate('metric_date', '<=', $to->toDateString());

        $summaryQuery = clone $query;

        return [
            'campaign_id' => $campaign->code,
            'campaign_name' => $campaign->name,
            'sponsor_name' => $campaign->sponsor?->name,
            'date_range' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'summary' => [
                'served_count' => (int) $summaryQuery->sum('served_count'),
                'rendered_count' => (int) $query->sum('rendered_count'),
                'qualified_impressions' => (int) $query->sum('qualified_impressions'),
                'clicks' => (int) $query->sum('clicks'),
                'ctr' => $this->safeRatio(
                    (int) $query->sum('clicks'),
                    (int) $query->sum('qualified_impressions')
                ),
                'unique_users_reached' => (int) $query->sum('unique_reach'),
            ],
            'by_placement' => (clone $query)
                ->select('placement_id')
                ->selectRaw('SUM(qualified_impressions) as qualified_impressions')
                ->selectRaw('SUM(clicks) as clicks')
                ->groupBy('placement_id')
                ->orderByDesc('qualified_impressions')
                ->get()
                ->map(function ($row): array {
                    return [
                        'placement_id' => $row->placement_id,
                        'qualified_impressions' => (int) $row->qualified_impressions,
                        'clicks' => (int) $row->clicks,
                        'ctr' => $this->safeRatio((int) $row->clicks, (int) $row->qualified_impressions),
                    ];
                }),
            'by_date' => (clone $query)
                ->select('metric_date')
                ->selectRaw('SUM(served_count) as served_count')
                ->selectRaw('SUM(rendered_count) as rendered_count')
                ->selectRaw('SUM(qualified_impressions) as qualified_impressions')
                ->selectRaw('SUM(clicks) as clicks')
                ->groupBy('metric_date')
                ->orderBy('metric_date')
                ->get(),
        ];
    }

    public function content(array $filters): array
    {
        [$from, $to] = $this->resolveRange($filters);

        $query = AggDailyContentMetric::query()
            ->whereDate('metric_date', '>=', $from->toDateString())
            ->whereDate('metric_date', '<=', $to->toDateString());

        if ($service = data_get($filters, 'service')) {
            $query->where('service', $service);
        }

        if ($contentType = data_get($filters, 'content_type')) {
            $query->where('content_type', $contentType);
        }

        $limit = (int) data_get($filters, 'limit', 25);

        return [
            'date_range' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'items' => $query
                ->select('service', 'content_type', 'content_id')
                ->selectRaw('SUM(opens) as opens')
                ->selectRaw('SUM(unique_users) as unique_users')
                ->selectRaw('SUM(completions) as completions')
                ->selectRaw('SUM(shares) as shares')
                ->selectRaw('AVG(avg_engagement_seconds) as avg_engagement_seconds')
                ->groupBy('service', 'content_type', 'content_id')
                ->orderByDesc('opens')
                ->limit($limit)
                ->get(),
        ];
    }

    public function live(array $filters): array
    {
        [$from, $to] = $this->resolveRange($filters);

        $query = AggHourlyServiceMetric::query()
            ->whereBetween('metric_hour', [$from->startOfDay(), $to->endOfDay()]);

        if ($service = data_get($filters, 'service')) {
            $query->where('service', $service);
        } else {
            $query->whereIn('service', ['media', 'match_center']);
        }

        if ($matchId = data_get($filters, 'match_id')) {
            $query->where('match_id', $matchId);
        }

        $limit = (int) data_get($filters, 'limit', 25);

        return [
            'date_range' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'summary' => [
                'audio_starts' => (int) $query->sum('audio_starts'),
                'listen_seconds_total' => (int) $query->sum('audio_listen_seconds'),
                'sponsor_impressions' => (int) $query->sum('sponsor_impressions'),
                'sponsor_clicks' => (int) $query->sum('sponsor_clicks'),
            ],
            'items' => (clone $query)
                ->select('metric_hour', 'service', 'surface', 'match_id', 'content_id')
                ->selectRaw('SUM(unique_users) as unique_users')
                ->selectRaw('SUM(audio_starts) as audio_starts')
                ->selectRaw('SUM(audio_listen_seconds) as audio_listen_seconds')
                ->selectRaw('SUM(sponsor_impressions) as sponsor_impressions')
                ->groupBy('metric_hour', 'service', 'surface', 'match_id', 'content_id')
                ->orderByDesc('audio_listen_seconds')
                ->limit($limit)
                ->get(),
        ];
    }

    private function resolveRange(array $filters): array
    {
        $to = isset($filters['date_to'])
            ? CarbonImmutable::parse($filters['date_to'])
            : CarbonImmutable::today();

        $from = isset($filters['date_from'])
            ? CarbonImmutable::parse($filters['date_from'])
            : $to->subDays(6);

        return [$from, $to];
    }

    private function uniqueUsersInRange(CarbonImmutable $from, CarbonImmutable $to, array $filters): int
    {
        return (int) AnalyticsEvent::query()
            ->when(data_get($filters, 'service'), fn (Builder $query, string $service) => $query->where('service', $service))
            ->when(data_get($filters, 'surface'), fn (Builder $query, string $surface) => $query->where('surface', $surface))
            ->when(data_get($filters, 'campaign_id'), fn (Builder $query, string $campaignId) => $query->where('campaign_id', $campaignId))
            ->whereDate('event_date', '>=', $from->toDateString())
            ->whereDate('event_date', '<=', $to->toDateString())
            ->selectRaw("COUNT(DISTINCT COALESCE(NULLIF(user_id, ''), anonymous_id)) as aggregate")
            ->value('aggregate');
    }

    private function safeRatio(int $numerator, int $denominator): float
    {
        if ($denominator === 0) {
            return 0.0;
        }

        return round($numerator / $denominator, 4);
    }
}
