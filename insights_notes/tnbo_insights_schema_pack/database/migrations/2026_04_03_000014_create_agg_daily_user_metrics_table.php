<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agg_daily_user_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('metric_date');
            $table->string('platform', 30)->nullable()->index();
            $table->unsignedBigInteger('dau')->default(0);
            $table->unsignedBigInteger('new_users')->default(0);
            $table->unsignedBigInteger('returning_users')->default(0);
            $table->decimal('avg_sessions_per_user', 8, 2)->default(0);
            $table->unsignedBigInteger('avg_session_duration_seconds')->default(0);
            $table->timestamps();

            $table->unique(['metric_date', 'platform'], 'uq_daily_user_platform');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agg_daily_user_metrics');
    }
};
