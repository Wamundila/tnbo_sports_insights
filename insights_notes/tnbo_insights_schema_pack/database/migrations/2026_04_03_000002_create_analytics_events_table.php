<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_uuid')->unique();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('user_id', 100)->nullable()->index();
            $table->string('anonymous_id', 100)->nullable()->index();
            $table->string('device_id', 150)->nullable()->index();
            $table->string('platform', 30)->nullable();
            $table->string('app_version', 30)->nullable();
            $table->string('event_name', 100)->index();
            $table->string('event_category', 60)->nullable()->index();
            $table->string('service', 60)->index();
            $table->string('surface', 100)->nullable()->index();
            $table->string('screen_name', 100)->nullable();
            $table->string('block_id', 100)->nullable()->index();
            $table->string('block_type', 60)->nullable();
            $table->unsignedBigInteger('placement_id')->nullable()->index();
            $table->integer('position_index')->nullable();
            $table->string('content_id', 100)->nullable()->index();
            $table->string('content_type', 60)->nullable();
            $table->string('match_id', 100)->nullable()->index();
            $table->string('competition_id', 100)->nullable()->index();
            $table->string('team_id', 100)->nullable()->index();
            $table->unsignedBigInteger('sponsor_id')->nullable()->index();
            $table->unsignedBigInteger('campaign_id')->nullable()->index();
            $table->unsignedBigInteger('creative_id')->nullable()->index();
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
