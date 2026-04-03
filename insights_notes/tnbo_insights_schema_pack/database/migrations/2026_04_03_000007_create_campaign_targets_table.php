<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('campaign_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('placement_id')->constrained('placements')->cascadeOnDelete();
            $table->string('service', 60)->nullable()->index();
            $table->string('surface', 100)->nullable()->index();
            $table->integer('priority')->default(0);
            $table->integer('max_impressions')->nullable();
            $table->integer('max_clicks')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['campaign_id', 'placement_id']);
            $table->index(['placement_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_targets');
    }
};
