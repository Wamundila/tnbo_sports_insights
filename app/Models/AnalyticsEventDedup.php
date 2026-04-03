<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEventDedup extends Model
{
    protected $table = 'analytics_event_dedup';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'first_seen_at' => 'immutable_datetime',
        ];
    }
}
