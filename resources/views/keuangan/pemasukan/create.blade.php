@extends('layouts.app')
@section('title', 'Tambah Pemasukan')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card>
        <x-slot name="title">Tambah Pemasukan</x-slot>
        <x-slot name="actions"><a href="{{ route('keuangan.pemasukan.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        <form action="{{ route('keuangan.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="jenis" value="PEMASUKAN">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold mb-1">Tanggal *</label><input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Kategori *</label><input type="text" list="catList" name="kategori" value="{{ old('kategori') }}" placeholder="Iuran Warga, Donasi..." class="w-full px-3 py-2 border rounded-md text-sm" required><datalist id="catList">@foreach($categories as $c)<option value="{{ $c }}">@endforeach</datalist></div>
                <div><label class="block text-sm font-semibold mb-1">Jumlah (Rp) *</label><input type="number" name="jumlah" value="{{ old('jumlah') }}" min="0" step="1000" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Sumber Dana</label><input type="text" name="sumber_dana" value="{{ old('sumber_dana') }}" placeholder="Kas RT, Donatur..." class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Keterangan</label><input type="text" name="keterangan" value="{{ old('keterangan') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Deskripsi</label><textarea name="deskripsi" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('deskripsi') }}</textarea></div>
            </div>
            <div class="flex justify-end gap-2 pt-4"><a href="{{ route('keuangan.pemasukan.index') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a><button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md text-sm font-semibold">Simpan</button></div>
        </form>
    </x-card>
</div>
@endsection
