<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_delivery_logs', function (Blueprint $table): void {
            $table->id();
            $table->uuid('delivery_id')->unique();
            $table->foreignId('sponsor_id')->nullable()->constrained('sponsors')->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->string('campaign_code', 100)->nullable()->index();
            $table->foreignId('creative_id')->nullable()->constrained('campaign_creatives')->nullOnDelete();
            $table->string('creative_code', 100)->nullable()->index();
            $table->foreignId('placement_id')->nullable()->constrained('placements')->nullOnDelete();
            $table->string('placement_code', 100)->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('user_id', 100)->nullable()->index();
            $table->string('anonymous_id', 100)->nullable()->index();
            $table->string('service', 60)->index();
            $table->string('surface', 100)->nullable()->index();
            $table->string('block_id', 100)->nullable()->index();
            $table->string('block_type', 100)->nullable();
            $table->string('content_id', 150)->nullable()->index();
            $table->string('match_id', 100)->nullable()->index();
            $table->string('competition_id', 100)->nullable()->index();
            $table->timestamp('served_at')->index();
            $table->json('response_context')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'served_at']);
            $table->index(['placement_id', 'served_at']);
            $table->index(['service', 'surface', 'served_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_delivery_logs');
    }
};
