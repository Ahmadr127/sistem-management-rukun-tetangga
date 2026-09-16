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
        Schema::create('warga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kartu_keluarga_id')
                ->nullable()
                ->constrained('kartu_keluarga')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('nik', 16)->unique();
            $table->string('nama');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('agama')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('status_perkawinan')->nullable();
            $table->string('hubungan_keluarga')->nullable();
            $table->string('kewarganegaraan', 10)->default('WNI');
            $table->string('golongan_darah', 3)->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->enum('status_warga', ['AKTIF', 'PINDAH', 'MENINGGAL'])->default('AKTIF');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warga');
    }
};
