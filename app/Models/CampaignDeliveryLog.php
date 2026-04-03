<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignDeliveryLog extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'served_at' => 'immutable_datetime',
            'response_context' => 'array',
        ];
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creative(): BelongsTo
    {
        return $this->belongsTo(CampaignCreative::class, 'creative_id');
    }

    public function placement(): BelongsTo
    {
        return $this->belongsTo(Placement::class);
    }
}
