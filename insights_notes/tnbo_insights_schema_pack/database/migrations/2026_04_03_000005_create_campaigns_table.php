<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_id')->constrained('sponsors')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('objective', 100)->nullable();
            $table->string('status', 40)->default('draft')->index();
            $table->timestamp('start_at')->nullable()->index();
            $table->timestamp('end_at')->nullable()->index();
            $table->decimal('budget_amount', 14, 2)->nullable();
            $table->integer('priority')->default(0);
            $table->integer('frequency_cap_per_user_per_day')->nullable();
            $table->json('targeting_config')->nullable();
            $table->string('reporting_label')->nullable();
            $table->timestamps();

            $table->index(['sponsor_id', 'status']);
            $table->index(['status', 'start_at', 'end_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
