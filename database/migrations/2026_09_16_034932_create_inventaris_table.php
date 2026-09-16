<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang');
            $table->string('kategori')->nullable()->comment('mebel, elektronik, alat kebersihan, dll');
            $table->integer('jumlah')->default(1);
            $table->string('satuan', 20)->default('unit');
            $table->enum('kondisi', ['BAIK', 'RUSAK_RINGAN', 'RUSAK_BERAT', 'HILANG'])->default('BAIK');
            $table->string('lokasi')->nullable()->comment('balai RT, gudang, dll');
            $table->decimal('harga_satuan', 15, 2)->nullable();
            $table->decimal('total_harga', 15, 2)->nullable();
            $table->string('sumber_dana')->nullable();
            $table->date('tanggal_pengadaan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
