<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agg_daily_content_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('metric_date');
            $table->string('service', 60)->index();
            $table->string('content_type', 60)->index();
            $table->string('content_id', 100)->index();
            $table->unsignedBigInteger('opens')->default(0);
            $table->unsignedBigInteger('unique_users')->default(0);
            $table->unsignedBigInteger('completions')->default(0);
            $table->unsignedBigInteger('shares')->default(0);
            $table->unsignedBigInteger('avg_engagement_seconds')->default(0);
            $table->timestamps();

            $table->unique(['metric_date', 'service', 'content_type', 'content_id'], 'uq_daily_content_metrics');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agg_daily_content_metrics');
    }
};
