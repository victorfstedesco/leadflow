<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = Schema::getColumnListing('posts');

        // Remove FK existente em campaign_id (se houver) antes de dropar a coluna.
        if (in_array('campaign_id', $columns, true)) {
            Schema::table('posts', function (Blueprint $table) {
                try {
                    $table->dropForeign(['campaign_id']);
                } catch (\Throwable $e) {
                    // Ignora se não existia FK explícita.
                }
            });
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('campaign_id');
            });
        }

        Schema::table('posts', function (Blueprint $table) use ($columns) {
            $toDrop = array_values(array_intersect(['campaign', 'scheduled_at', 'post_status'], $columns));
            if (! empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('campaign_id')
                ->nullable()
                ->after('objective')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campaign_id');
        });
    }
};
