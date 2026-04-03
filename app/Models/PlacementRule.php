<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlacementRule extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'rule_value' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function placement(): BelongsTo
    {
        return $this->belongsTo(Placement::class);
    }
}
