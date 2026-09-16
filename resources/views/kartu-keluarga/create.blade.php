@extends('layouts.app')
@section('title', 'Tambah KK')
@section('content')
<div class="max-w-3xl mx-auto">
    <x-card>
        <x-slot name="title">Tambah Kartu Keluarga</x-slot>
        <x-slot name="actions"><a href="{{ route('kartu-keluarga.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        <form action="{{ route('kartu-keluarga.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold mb-1">No KK (16 digit) *</label><input type="text" name="no_kk" value="{{ old('no_kk') }}" maxlength="16" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Kepala Keluarga</label><input type="text" name="kepala_keluarga" value="{{ old('kepala_keluarga') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Alamat</label><textarea name="alamat" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('alamat') }}</textarea></div>
                <div><label class="block text-sm font-semibold mb-1">RT</label><input type="text" name="rt" value="{{ old('rt') }}" maxlength="3" placeholder="001" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">RW</label><input type="text" name="rw" value="{{ old('rw') }}" maxlength="3" placeholder="002" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Dusun</label><input type="text" name="dusun" value="{{ old('dusun') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Desa</label><input type="text" name="desa" value="{{ old('desa') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kecamatan</label><input type="text" name="kecamatan" value="{{ old('kecamatan') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kabupaten</label><input type="text" name="kabupaten" value="{{ old('kabupaten') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Provinsi</label><input type="text" name="provinsi" value="{{ old('provinsi') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kode Pos</label><input type="text" name="kode_pos" value="{{ old('kode_pos') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
            </div>
            <div class="flex justify-end gap-2 pt-4"><a href="{{ route('kartu-keluarga.index') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a><button type="submit" class="px-6 py-2 bg-sp-primary text-white rounded-md text-sm font-semibold">Simpan</button></div>
        </form>
    </x-card>
</div>
@endsection
