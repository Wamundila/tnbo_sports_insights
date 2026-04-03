<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('analytics_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100)->unique();
            $table->string('user_id', 100)->nullable()->index();
            $table->string('anonymous_id', 100)->nullable()->index();
            $table->string('device_id', 150)->nullable();
            $table->string('platform', 30)->nullable();
            $table->string('app_version', 30)->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('city', 100)->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_sessions');
    }
};
