<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Limpa tabelas legadas de uma tentativa anterior abandonada.
        Schema::dropIfExists('campaign_metrics');
        Schema::dropIfExists('post_metrics');
        Schema::dropIfExists('client_reports');
        Schema::dropIfExists('campaigns');

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('meta_campaign_id')->index();
            $table->string('name');
            $table->string('objective')->nullable();
            $table->string('meta_status')->nullable();
            $table->date('start_date')->nullable();
            $table->date('stop_date')->nullable();
            $table->decimal('daily_budget', 12, 2)->nullable();
            $table->decimal('lifetime_budget', 12, 2)->nullable();
            $table->json('insights')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'meta_campaign_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
