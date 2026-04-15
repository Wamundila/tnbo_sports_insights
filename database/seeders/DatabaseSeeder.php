<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment(['local', 'testing'])) {
            User::query()->firstOrCreate(
                ['email' => 'chilandosongwe@gmail.com'],
                [
                    'name' => 'Wamundila',
                    'password' => bcrypt('Xpat7007!!'),
                ]
            );
        }

        $this->call(InsightsStarterSeeder::class);
    }
}
