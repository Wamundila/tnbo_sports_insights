<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agg_daily_user_metrics', function (Blueprint $table): void {
            $table->id();
            $table->date('metric_date');
            $table->string('platform', 30)->nullable()->index();
            $table->unsignedBigInteger('dau')->default(0);
            $table->unsignedBigInteger('new_users')->default(0);
            $table->unsignedBigInteger('returning_users')->default(0);
            $table->unsignedBigInteger('avg_sessions_per_user')->default(0);
            $table->unsignedBigInteger('avg_session_duration_seconds')->default(0);
            $table->timestamps();

            $table->unique(['metric_date', 'platform'], 'agg_daily_user_metrics_scope');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agg_daily_user_metrics');
    }
};
