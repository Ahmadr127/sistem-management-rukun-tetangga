<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mutasi_warga', function (Blueprint $table) {
            $table->id();

            $table->foreignId('warga_id')
                ->constrained('warga')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('jenis_mutasi', [
                'LAHIR',
                'MASUK',
                'KELUAR',
                'PINDAH_KK',
                'MENINGGAL',
            ]);

            $table->date('tanggal_mutasi');

            // KK sebelum mutasi
            $table->foreignId('kk_lama_id')
                ->nullable()
                ->constrained('kartu_keluarga')
                ->nullOnDelete();

            // KK setelah mutasi
            $table->foreignId('kk_baru_id')
                ->nullable()
                ->constrained('kartu_keluarga')
                ->nullOnDelete();

            $table->string('alamat_asal')->nullable();
            $table->string('alamat_tujuan')->nullable();

            $table->text('alasan')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_warga');
    }
};
