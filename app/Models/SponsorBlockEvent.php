<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorBlockEvent extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'immutable_datetime',
            'properties' => 'array',
        ];
    }

    public function deliveryLog(): BelongsTo
    {
        return $this->belongsTo(CampaignDeliveryLog::class, 'delivery_log_id');
    }
}
