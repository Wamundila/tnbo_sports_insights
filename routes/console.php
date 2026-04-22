<?php

use App\Models\AnalyticsEvent;
use App\Services\Analytics\RawEventRetentionService;
use App\Services\Reporting\AggregationRollupService;
use App\Support\ReportingTime;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Symfony\Component\Console\Command\Command as ConsoleCommand;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('insights:rollup-daily {--date=}', function (AggregationRollupService $service) {
    $date = $this->option('date')
        ? CarbonImmutable::parse($this->option('date'))
        : CarbonImmutable::yesterday();

    $service->rollupDate($date);

    $this->info('Rolled up daily metrics for '.$date->toDateString());
})->purpose('Roll up TNBO Insights daily aggregate metrics');

Artisan::command('insights:rollup-today', function (AggregationRollupService $service) {
    $date = CarbonImmutable::today();

    $service->rollupDate($date);

    $this->info('Refreshed current-day metrics for '.$date->toDateString());
})->purpose('Refresh current-day TNBO Insights aggregate metrics for dashboard visibility');

Artisan::command('insights:rollup-hourly {--hour=}', function (AggregationRollupService $service) {
    $hour = $this->option('hour')
        ? CarbonImmutable::parse($this->option('hour'))
        : CarbonImmutable::now()->subHour()->startOfHour();

    $service->rollupHour($hour);

    $this->info('Rolled up hourly metrics for '.$hour->toDateTimeString());
})->purpose('Roll up TNBO Insights hourly aggregate metrics');

Artisan::command('insights:prune-raw-events {--before-date=} {--retention-days=}', function (RawEventRetentionService $service) {
    $retentionDays = $this->option('retention-days') !== null
        ? (int) $this->option('retention-days')
        : (int) config('insights.raw_event_retention_days', 90);

    if ($retentionDays < 1) {
        $this->error('Retention days must be at least 1.');

        return ConsoleCommand::FAILURE;
    }

    $beforeDate = $this->option('before-date')
        ? CarbonImmutable::parse($this->option('before-date'))
        : CarbonImmutable::today()->subDays($retentionDays);

    $result = $service->pruneBefore($beforeDate);

    $this->info(sprintf(
        'Pruned raw events before %s. Deleted %d analytics events and %d dedup rows.',
        $result['before_date'],
        $result['deleted_events'],
        $result['deleted_dedup']
    ));

    return ConsoleCommand::SUCCESS;
})->purpose('Prune old TNBO Insights raw analytics events and dedup records');

Artisan::command('insights:repair-event-dates {--from-date=} {--to-date=}', function () {
    $query = AnalyticsEvent::query();

    if ($fromDate = $this->option('from-date')) {
        $query->whereDate('occurred_at', '>=', $fromDate);
    }

    if ($toDate = $this->option('to-date')) {
        $query->whereDate('occurred_at', '<=', $toDate);
    }

    $scanned = 0;
    $updated = 0;

    $query->orderBy('id')->chunkById(1000, function ($events) use (&$scanned, &$updated): void {
        foreach ($events as $event) {
            $scanned++;

            $eventDate = ReportingTime::eventDateFromStoredUtc($event->getRawOriginal('occurred_at'));

            if ($event->event_date?->toDateString() === $eventDate) {
                continue;
            }

            $event->forceFill(['event_date' => $eventDate])->saveQuietly();
            $updated++;
        }
    });

    $this->info("Scanned {$scanned} analytics events. Repaired {$updated} event_date values.");

    return ConsoleCommand::SUCCESS;
})->purpose('Repair analytics event_date values from occurred_at using the reporting timezone');

Schedule::command('insights:rollup-hourly')->hourly();
Schedule::command('insights:rollup-today')->hourly();
Schedule::command('insights:rollup-daily')->dailyAt('01:00');
Schedule::command('insights:prune-raw-events')->dailyAt('02:00');
