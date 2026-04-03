<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_at' => 'immutable_datetime',
            'end_at' => 'immutable_datetime',
            'targeting_json' => 'array',
            'frequency_cap_json' => 'array',
        ];
    }

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function creatives(): HasMany
    {
        return $this->hasMany(CampaignCreative::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(CampaignTarget::class);
    }
}
