<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('placements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('service', 60)->index();
            $table->string('surface', 100)->index();
            $table->string('block_type', 60);
            $table->string('allowed_creative_type', 60)->nullable();
            $table->string('position_hint', 60)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['service', 'surface', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placements');
    }
};
