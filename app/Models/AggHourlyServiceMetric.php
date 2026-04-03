<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AggHourlyServiceMetric extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'metric_hour' => 'immutable_datetime',
        ];
    }
}
