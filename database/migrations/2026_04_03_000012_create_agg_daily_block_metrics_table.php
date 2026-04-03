<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agg_daily_block_metrics', function (Blueprint $table): void {
            $table->id();
            $table->date('metric_date');
            $table->string('service', 60)->index();
            $table->string('surface', 100)->index();
            $table->string('block_id', 100)->index();
            $table->string('block_type', 100)->nullable();
            $table->string('placement_id', 100)->nullable()->index();
            $table->unsignedBigInteger('block_views')->default(0);
            $table->unsignedBigInteger('block_clicks')->default(0);
            $table->unsignedBigInteger('unique_viewers')->default(0);
            $table->unsignedBigInteger('sponsor_impressions')->default(0);
            $table->unsignedBigInteger('sponsor_clicks')->default(0);
            $table->decimal('ctr', 8, 4)->default(0);
            $table->timestamps();

            $table->unique(['metric_date', 'service', 'surface', 'block_id', 'placement_id'], 'agg_daily_block_metrics_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agg_daily_block_metrics');
    }
};
