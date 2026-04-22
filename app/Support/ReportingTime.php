<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use DateTimeInterface;

class ReportingTime
{
    public static function timezone(): string
    {
        return (string) config('insights.reporting_timezone', config('app.timezone', 'UTC'));
    }

    public static function utcInstant(DateTimeInterface|string $timestamp): CarbonImmutable
    {
        return self::toCarbon($timestamp)->utc();
    }

    public static function eventDate(DateTimeInterface|string $timestamp): string
    {
        return self::utcInstant($timestamp)
            ->setTimezone(self::timezone())
            ->toDateString();
    }

    public static function eventDateFromStoredUtc(DateTimeInterface|string $timestamp): string
    {
        if ($timestamp instanceof DateTimeInterface) {
            return self::eventDate($timestamp);
        }

        return CarbonImmutable::parse($timestamp, 'UTC')
            ->setTimezone(self::timezone())
            ->toDateString();
    }

    private static function toCarbon(DateTimeInterface|string $timestamp): CarbonImmutable
    {
        if ($timestamp instanceof DateTimeInterface) {
            return CarbonImmutable::instance($timestamp);
        }

        return CarbonImmutable::parse($timestamp, 'UTC');
    }
}
