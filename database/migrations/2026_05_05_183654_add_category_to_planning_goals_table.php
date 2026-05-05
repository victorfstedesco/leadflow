<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planning_goals', function (Blueprint $table) {
            $table->string('category', 32)->nullable()->after('planning_id');
            $table->string('title', 160)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('planning_goals', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->string('title', 160)->nullable(false)->change();
        });
    }
};
