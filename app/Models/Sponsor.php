<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sponsor extends Model
{
    protected $guarded = [];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
