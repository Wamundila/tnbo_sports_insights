<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('placement_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('placement_id')->constrained('placements')->cascadeOnDelete();
            $table->string('rule_type', 60);
            $table->string('rule_operator', 30);
            $table->json('rule_value');
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placement_rules');
    }
};
