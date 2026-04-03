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
                ['email' => 'admin@tnbo.test'],
                [
                    'name' => 'TNBO Admin',
                    'password' => bcrypt('password'),
                ]
            );
        }

        $this->call(InsightsStarterSeeder::class);
    }
}
