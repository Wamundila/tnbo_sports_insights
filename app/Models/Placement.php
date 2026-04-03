<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Placement extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'allowed_creative_types' => 'array',
            'default_rules' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function rules(): HasMany
    {
        return $this->hasMany(PlacementRule::class);
    }

    public function campaignTargets(): HasMany
    {
        return $this->hasMany(CampaignTarget::class);
    }
}
