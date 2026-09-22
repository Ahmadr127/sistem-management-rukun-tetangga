<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_jenis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rt_id')->constrained('rts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('nama', 100);
            $table->enum('periode_type', ['weekly', 'monthly', 'yearly'])->default('monthly');
            $table->decimal('nominal', 12, 2)->default(0);
            $table->enum('target_type', ['kk', 'perorangan'])->default('perorangan');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('rt_id');
            $table->index('periode_type');
            $table->index('is_active');
        });

        Schema::create('kas_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kas_jenis_id')->constrained('kas_jenis')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('kartu_keluarga_id')->nullable()->constrained('kartu_keluarga')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('warga_id')->nullable()->constrained('warga')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('nominal_bayar', 12, 2)->default(0);
            $table->dateTime('waktu_bayar')->nullable();
            $table->enum('status', ['belum_bayar', 'sudah_bayar'])->default('sudah_bayar');
            $table->string('catatan', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['kas_jenis_id', 'tanggal']);
            $table->index('kartu_keluarga_id');
            $table->index('warga_id');
            // Catatan: kolom nullable membuat MySQL menganggap NULL sebagai nilai
            // berbeda, sehingga duplikat tetap dicegah di level aplikasi (service).
            $table->unique(
                ['kas_jenis_id', 'tanggal', 'warga_id', 'kartu_keluarga_id'],
                'kas_bayar_unique_sel'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_pembayaran');
        Schema::dropIfExists('kas_jenis');
    }
};
