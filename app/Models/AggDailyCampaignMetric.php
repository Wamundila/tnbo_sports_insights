<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AggDailyCampaignMetric extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'metric_date' => 'date',
            'ctr' => 'decimal:4',
            'spend_estimate' => 'decimal:2',
        ];
    }
}
