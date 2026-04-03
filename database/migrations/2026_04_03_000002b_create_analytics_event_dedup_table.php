<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_event_dedup', function (Blueprint $table): void {
            $table->id();
            $table->string('event_id', 100)->unique();
            $table->timestamp('first_seen_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_event_dedup');
    }
};
