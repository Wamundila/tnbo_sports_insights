<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignTarget extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'constraints_json' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function placement(): BelongsTo
    {
        return $this->belongsTo(Placement::class);
    }
}
