<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('meta_user_id')->nullable()->after('notes');
            $table->text('meta_access_token')->nullable()->after('meta_user_id');
            $table->string('meta_ad_account_id')->nullable()->after('meta_access_token');
            $table->timestamp('meta_token_expires_at')->nullable()->after('meta_ad_account_id');
            $table->timestamp('meta_last_synced_at')->nullable()->after('meta_token_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'meta_user_id',
                'meta_access_token',
                'meta_ad_account_id',
                'meta_token_expires_at',
                'meta_last_synced_at',
            ]);
        });
    }
};
