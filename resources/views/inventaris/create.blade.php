@extends('layouts.app')
@section('title', 'Tambah Inventaris')
@section('content')
<div class="max-w-3xl mx-auto">
    <x-card>
        <x-slot name="title">Tambah Inventaris</x-slot>
        <x-slot name="actions"><a href="{{ route('inventaris.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        <form action="{{ route('inventaris.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold mb-1">Kode Barang *</label><input type="text" name="kode_barang" value="{{ old('kode_barang') }}" placeholder="BRG-001" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Nama Barang *</label><input type="text" name="nama_barang" value="{{ old('nama_barang') }}" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Kategori</label><input type="text" name="kategori" value="{{ old('kategori') }}" placeholder="Mebel, Elektronik..." class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Jumlah *</label><input type="number" name="jumlah" value="{{ old('jumlah',1) }}" min="1" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Satuan</label><input type="text" name="satuan" value="{{ old('satuan','unit') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kondisi *</label><select name="kondisi" class="w-full px-3 py-2 border rounded-md text-sm"><option value="BAIK" {{ old('kondisi')=='BAIK'?'selected':'' }}>Baik</option><option value="RUSAK_RINGAN" {{ old('kondisi')=='RUSAK_RINGAN'?'selected':'' }}>Rusak Ringan</option><option value="RUSAK_BERAT" {{ old('kondisi')=='RUSAK_BERAT'?'selected':'' }}>Rusak Berat</option><option value="HILANG" {{ old('kondisi')=='HILANG'?'selected':'' }}>Hilang</option></select></div>
                <div><label class="block text-sm font-semibold mb-1">Lokasi</label><input type="text" name="lokasi" value="{{ old('lokasi') }}" placeholder="Balai RT" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Harga Satuan (Rp)</label><input type="number" name="harga_satuan" value="{{ old('harga_satuan') }}" min="0" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Sumber Dana</label><input type="text" name="sumber_dana" value="{{ old('sumber_dana') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Tanggal Pengadaan</label><input type="date" name="tanggal_pengadaan" value="{{ old('tanggal_pengadaan') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Keterangan</label><textarea name="keterangan" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('keterangan') }}</textarea></div>
            </div>
            <div class="flex justify-end gap-2 pt-4"><a href="{{ route('inventaris.index') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a><button type="submit" class="px-6 py-2 bg-sp-primary text-white rounded-md text-sm font-semibold">Simpan</button></div>
        </form>
    </x-card>
</div>
@endsection
