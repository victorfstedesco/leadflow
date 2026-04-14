<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['platform', 'status', 'scheduled_date']);
            $table->string('campaign')->nullable()->after('objective');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('platform')->nullable();
            $table->string('status')->default('rascunho');
            $table->date('scheduled_date')->nullable();
            $table->dropColumn('campaign');
        });
    }
};
