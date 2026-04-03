<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('campaign_creatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->string('creative_type', 60)->index();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->string('label')->nullable();
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('audio_url')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('destination_type', 60)->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_creatives');
    }
};
