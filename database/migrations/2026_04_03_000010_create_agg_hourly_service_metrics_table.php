<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agg_hourly_service_metrics', function (Blueprint $table): void {
            $table->id();
            $table->timestamp('metric_hour');
            $table->string('service', 60)->index();
            $table->string('surface', 100)->nullable()->index();
            $table->string('platform', 30)->nullable()->index();
            $table->string('match_id', 100)->nullable()->index();
            $table->string('content_id', 150)->nullable()->index();
            $table->unsignedBigInteger('sessions')->default(0);
            $table->unsignedBigInteger('unique_users')->default(0);
            $table->unsignedBigInteger('screen_views')->default(0);
            $table->unsignedBigInteger('content_opens')->default(0);
            $table->unsignedBigInteger('sponsor_impressions')->default(0);
            $table->unsignedBigInteger('sponsor_clicks')->default(0);
            $table->unsignedBigInteger('audio_starts')->default(0);
            $table->unsignedBigInteger('audio_listen_seconds')->default(0);
            $table->unsignedBigInteger('game_starts')->default(0);
            $table->unsignedBigInteger('poll_votes')->default(0);
            $table->timestamps();

            $table->unique(['metric_hour', 'service', 'surface', 'platform', 'match_id', 'content_id'], 'agg_hourly_service_metrics_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agg_hourly_service_metrics');
    }
};
