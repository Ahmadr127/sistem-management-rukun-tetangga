<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kartu_keluarga', function (Blueprint $table) {
            $table->foreignId('rt_id')->nullable()->after('kode_pos')->constrained('rts')->nullOnDelete()->cascadeOnUpdate();
            $table->index('rt_id');
        });

        // Backfill existing string rt -> rt_id if rts already exist (fresh will not need)
        // This will be handled in seeder/migration after rts seed
    }

    public function down(): void
    {
        Schema::table('kartu_keluarga', function (Blueprint $table) {
            $table->dropForeign(['rt_id']);
            $table->dropColumn('rt_id');
        });
    }
};
