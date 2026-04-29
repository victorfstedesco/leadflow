<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planning_campaign', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('local_status')->default('em_execucao'); // em_execucao, pausada, concluida
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['planning_id', 'campaign_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planning_campaign');
    }
};
