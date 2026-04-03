<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table): void {
            $table->id();
            $table->string('event_id', 100)->unique();
            $table->unsignedInteger('schema_version')->default(1);
            $table->string('session_id', 100)->nullable()->index();
            $table->string('user_id', 100)->nullable()->index();
            $table->string('anonymous_id', 100)->nullable()->index();
            $table->string('device_id', 150)->nullable()->index();
            $table->string('platform', 30)->nullable()->index();
            $table->string('app_version', 30)->nullable();
            $table->string('event_name', 100)->index();
            $table->string('event_category', 60)->nullable()->index();
            $table->string('service', 60)->index();
            $table->string('surface', 100)->nullable()->index();
            $table->string('screen_name', 100)->nullable();
            $table->string('block_id', 100)->nullable()->index();
            $table->string('block_type', 100)->nullable()->index();
            $table->string('placement_id', 100)->nullable()->index();
            $table->integer('position_index')->nullable();
            $table->string('content_id', 150)->nullable()->index();
            $table->string('content_type', 100)->nullable()->index();
            $table->string('campaign_id', 100)->nullable()->index();
            $table->string('creative_id', 100)->nullable()->index();
            $table->string('match_id', 100)->nullable()->index();
            $table->string('competition_id', 100)->nullable()->index();
            $table->string('team_id', 100)->nullable()->index();
            $table->timestamp('occurred_at')->index();
            $table->date('event_date')->index();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['service', 'event_date']);
            $table->index(['service', 'surface', 'event_date']);
            $table->index(['campaign_id', 'event_date']);
            $table->index(['placement_id', 'event_date']);
            $table->index(['content_type', 'content_id', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
