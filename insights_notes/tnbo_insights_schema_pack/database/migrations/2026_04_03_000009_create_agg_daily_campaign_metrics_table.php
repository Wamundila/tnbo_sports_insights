<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agg_daily_campaign_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('metric_date');
            $table->foreignId('sponsor_id')->nullable()->constrained('sponsors')->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->foreignId('creative_id')->nullable()->constrained('campaign_creatives')->nullOnDelete();
            $table->foreignId('placement_id')->nullable()->constrained('placements')->nullOnDelete();
            $table->string('service', 60)->nullable()->index();
            $table->string('surface', 100)->nullable()->index();
            $table->unsignedBigInteger('served_count')->default(0);
            $table->unsignedBigInteger('rendered_count')->default(0);
            $table->unsignedBigInteger('qualified_impressions')->default(0);
            $table->unsignedBigInteger('unique_reach')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('completions')->default(0);
            $table->decimal('ctr', 8, 4)->default(0);
            $table->decimal('spend_estimate', 14, 2)->nullable();
            $table->timestamps();

            $table->index(['metric_date', 'campaign_id']);
            $table->index(['metric_date', 'placement_id']);
            $table->index(['metric_date', 'service', 'surface']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agg_daily_campaign_metrics');
    }
};
