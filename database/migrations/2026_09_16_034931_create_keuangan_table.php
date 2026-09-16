<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('jenis', ['PEMASUKAN', 'PENGELUARAN']);
            $table->string('kategori');
            $table->decimal('jumlah', 15, 2);
            $table->string('sumber_dana')->nullable()->comment('untuk pemasukan: iuran, donasi, dll; untuk pengeluaran: kas RT, dll');
            $table->string('keterangan')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('bukti')->nullable()->comment('path file bukti');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan');
    }
};
