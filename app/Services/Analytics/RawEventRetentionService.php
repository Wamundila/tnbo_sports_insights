<?php

namespace App\Services\Analytics;

use App\Models\AnalyticsEvent;
use App\Models\AnalyticsEventDedup;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

class RawEventRetentionService
{
    public function pruneBefore(CarbonImmutable $beforeDate): array
    {
        $eventCutoff = $beforeDate->toDateString();
        $dedupCutoff = $beforeDate->startOfDay();

        return [
            'deleted_events' => $this->deleteInChunks(
                AnalyticsEvent::query()->whereDate('event_date', '<', $eventCutoff)
            ),
            'deleted_dedup' => $this->deleteInChunks(
                AnalyticsEventDedup::query()->where('first_seen_at', '<', $dedupCutoff)
            ),
            'before_date' => $eventCutoff,
        ];
    }

    private function deleteInChunks(Builder $query, int $chunkSize = 1000): int
    {
        $deleted = 0;

        $query
            ->orderBy('id')
            ->chunkById($chunkSize, function ($records) use (&$deleted): void {
                if ($records->isEmpty()) {
                    return;
                }

                $deleted += $records
                    ->first()
                    ->newQuery()
                    ->whereKey($records->pluck('id'))
                    ->delete();
            });

        return $deleted;
    }
}
