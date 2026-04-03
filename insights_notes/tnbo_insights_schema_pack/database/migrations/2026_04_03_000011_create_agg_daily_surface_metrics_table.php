<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agg_daily_surface_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('metric_date');
            $table->string('service', 60)->index();
            $table->string('surface', 100)->index();
            $table->string('platform', 30)->nullable()->index();
            $table->unsignedBigInteger('sessions')->default(0);
            $table->unsignedBigInteger('unique_users')->default(0);
            $table->unsignedBigInteger('screen_views')->default(0);
            $table->unsignedBigInteger('avg_time_spent_seconds')->default(0);
            $table->unsignedBigInteger('exits')->default(0);
            $table->unsignedBigInteger('sponsor_impressions')->default(0);
            $table->unsignedBigInteger('sponsor_clicks')->default(0);
            $table->timestamps();

            $table->unique(['metric_date', 'service', 'surface', 'platform'], 'uq_daily_surface_platform');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agg_daily_surface_metrics');
    }
};
