<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventaris', function (Blueprint $table) {
            $table->text('foto')->nullable()->change();
        });
        Schema::table('peminjaman_inventaris', function (Blueprint $table) {
            $table->text('foto_kembali')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('inventaris', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->change();
        });
        Schema::table('peminjaman_inventaris', function (Blueprint $table) {
            $table->string('foto_kembali', 255)->nullable()->change();
        });
    }
};
