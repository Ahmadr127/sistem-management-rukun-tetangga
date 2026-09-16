@extends('layouts.app')
@section('title', 'Pinjam Barang')
@section('content')
<div class="max-w-3xl mx-auto">
    <x-card>
        <x-slot name="title">Pinjam Inventaris</x-slot>
        <x-slot name="actions"><a href="{{ route('peminjaman-inventaris.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        <form action="{{ route('peminjaman-inventaris.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Barang *</label>
                    <select name="inventaris_id" class="w-full px-3 py-2 border rounded-md text-sm" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach($inventaris as $inv)<option value="{{ $inv->id }}" {{ old('inventaris_id')==$inv->id?'selected':'' }}>{{ $inv->kode_barang }} - {{ $inv->nama_barang }} (Stok: {{ $inv->stok_tersedia }}/{{ $inv->jumlah }})</option>@endforeach
                    </select>
                    @error('inventaris_id')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div><label class="block text-sm font-semibold mb-1">Jumlah Pinjam *</label><input type="number" name="jumlah_pinjam" value="{{ old('jumlah_pinjam',1) }}" min="1" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Warga (jika terdaftar)</label>
                    <select name="warga_id" class="w-full px-3 py-2 border rounded-md text-sm">
                        <option value="">-- Non-warga / isi nama manual --</option>
                        @foreach($warga as $w)<option value="{{ $w->id }}" {{ old('warga_id')==$w->id?'selected':'' }}>{{ $w->nama }} - {{ $w->nik }}</option>@endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-semibold mb-1">Nama Peminjam (jika non-warga)</label><input type="text" name="nama_peminjam" value="{{ old('nama_peminjam') }}" class="w-full px-3 py-2 border rounded-md text-sm" placeholder="Isi jika bukan warga terdaftar"></div>
                <div><label class="block text-sm font-semibold mb-1">No HP</label><input type="text" name="no_hp_peminjam" value="{{ old('no_hp_peminjam') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Tanggal Pinjam *</label><input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Rencana Kembali</label><input type="date" name="tanggal_kembali_rencana" value="{{ old('tanggal_kembali_rencana') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Keperluan</label><textarea name="keperluan" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('keperluan') }}</textarea></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Keterangan</label><textarea name="keterangan" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('keterangan') }}</textarea></div>
            </div>
            <div class="flex justify-end gap-2 pt-4"><a href="{{ route('peminjaman-inventaris.index') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a><button type="submit" class="px-6 py-2 bg-sp-primary text-white rounded-md text-sm font-semibold">Simpan</button></div>
        </form>
    </x-card>
</div>
@endsection
