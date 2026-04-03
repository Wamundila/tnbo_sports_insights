<?php

namespace Database\Seeders;

use App\Models\Placement;
use Illuminate\Database\Seeder;

class InsightsStarterSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('insights.starter_placements', []) as $placement) {
            Placement::query()->updateOrCreate(
                ['code' => $placement['code']],
                $placement
            );
        }
    }
}
