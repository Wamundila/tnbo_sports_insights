<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_creatives', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('creative_type', 60)->index();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->string('label_text')->nullable();
            $table->string('image_url')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('audio_url')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->json('metadata_json')->nullable();
            $table->string('status', 40)->default('active')->index();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_creatives');
    }
};
