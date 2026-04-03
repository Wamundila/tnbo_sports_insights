<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sponsor_block_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_uuid')->unique();
            $table->foreignId('delivery_log_id')->nullable()->constrained('campaign_delivery_logs')->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->foreignId('creative_id')->nullable()->constrained('campaign_creatives')->nullOnDelete();
            $table->foreignId('placement_id')->nullable()->constrained('placements')->nullOnDelete();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('user_id', 100)->nullable()->index();
            $table->string('anonymous_id', 100)->nullable()->index();
            $table->string('event_name', 100)->index();
            $table->string('service', 60)->index();
            $table->string('surface', 100)->nullable()->index();
            $table->timestamp('occurred_at')->index();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'occurred_at']);
            $table->index(['placement_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsor_block_events');
    }
};
