<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman_inventaris', function (Blueprint $table) {
            $table->string('foto_kembali', 255)->nullable()->after('kondisi_kembali')->comment('foto kondisi saat pengembalian');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman_inventaris', function (Blueprint $table) {
            $table->dropColumn('foto_kembali');
        });
    }
};
