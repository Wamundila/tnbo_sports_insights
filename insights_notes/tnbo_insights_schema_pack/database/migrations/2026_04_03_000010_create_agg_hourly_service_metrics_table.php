<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agg_hourly_service_metrics', function (Blueprint $table) {
            $table->id();
            $table->dateTime('metric_hour');
            $table->string('service', 60)->index();
            $table->string('platform', 30)->nullable()->index();
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

            $table->unique(['metric_hour', 'service', 'platform'], 'uq_hourly_service_platform');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agg_hourly_service_metrics');
    }
};
