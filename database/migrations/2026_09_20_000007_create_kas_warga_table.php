<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_warga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rt_id')->constrained('rts')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('warga_id')->constrained('warga')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('periode_type', ['weekly','monthly'])->default('monthly');
            $table->string('periode', 20); // e.g. 2026-01 or 2026-W01
            $table->decimal('nominal', 12, 2)->default(0);
            $table->date('tanggal_bayar')->nullable();
            $table->enum('status', ['belum_bayar','sudah_bayar'])->default('belum_bayar');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['warga_id','periode_type','periode'], 'kas_warga_unique_periode');
            $table->index('rt_id');
            $table->index('warga_id');
            $table->index('periode');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_warga');
    }
};
