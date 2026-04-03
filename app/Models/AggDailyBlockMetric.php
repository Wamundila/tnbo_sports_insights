<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AggDailyBlockMetric extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'metric_date' => 'date',
            'ctr' => 'decimal:4',
        ];
    }
}
