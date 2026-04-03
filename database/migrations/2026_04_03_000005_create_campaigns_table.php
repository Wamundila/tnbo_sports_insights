<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sponsor_id')->constrained('sponsors')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('objective', 100)->nullable();
            $table->string('status', 40)->default('draft')->index();
            $table->timestamp('start_at')->nullable()->index();
            $table->timestamp('end_at')->nullable()->index();
            $table->integer('priority')->default(0);
            $table->text('budget_notes')->nullable();
            $table->json('targeting_json')->nullable();
            $table->json('frequency_cap_json')->nullable();
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
