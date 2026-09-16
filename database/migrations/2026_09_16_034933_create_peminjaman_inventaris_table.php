<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_inventaris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventaris_id')->constrained('inventaris')->cascadeOnDelete();
            $table->foreignId('warga_id')->nullable()->constrained('warga')->nullOnDelete();
            $table->string('nama_peminjam')->nullable()->comment('jika bukan warga terdaftar');
            $table->string('no_hp_peminjam', 20)->nullable();
            $table->integer('jumlah_pinjam')->default(1);
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali_rencana')->nullable();
            $table->date('tanggal_kembali_aktual')->nullable();
            $table->enum('status', ['DIPINJAM', 'DIKEMBALIKAN', 'TERLAMBAT', 'HILANG', 'RUSAK'])->default('DIPINJAM');
            $table->text('keperluan')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('kondisi_kembali')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_inventaris');
    }
};
