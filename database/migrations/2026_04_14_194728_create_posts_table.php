<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('copy')->nullable();
            $table->string('content_type'); // imagem, video, carrossel, story, reels
            $table->string('objective')->nullable(); // engajamento, conversao, branding, educacao
            $table->string('platform')->nullable(); // Instagram, TikTok, Facebook, LinkedIn, YouTube, X
            $table->string('status')->default('rascunho'); // rascunho, em_producao, agendada, publicada
            $table->date('scheduled_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
