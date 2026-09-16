@extends('layouts.app')
@section('title', 'Catat Mutasi')
@section('content')
<div class="max-w-3xl mx-auto">
    <x-card>
        <x-slot name="title">Catat Mutasi Warga</x-slot>
        <x-slot name="actions"><a href="{{ route('mutasi-warga.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        <form action="{{ route('mutasi-warga.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold mb-1">Warga *</label><select name="warga_id" class="w-full px-3 py-2 border rounded-md text-sm" required><option value="">-- Pilih Warga --</option>@foreach($warga as $w)<option value="{{ $w->id }}" {{ old('warga_id')==$w->id?'selected':'' }}>{{ $w->nama }} - {{ $w->nik }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">Jenis Mutasi *</label><select name="jenis_mutasi" class="w-full px-3 py-2 border rounded-md text-sm" required><option value="LAHIR" {{ old('jenis_mutasi')=='LAHIR'?'selected':'' }}>Kelahiran</option><option value="MASUK" {{ old('jenis_mutasi')=='MASUK'?'selected':'' }}>Masuk (Pindahan)</option><option value="KELUAR" {{ old('jenis_mutasi')=='KELUAR'?'selected':'' }}>Keluar (Pindah)</option><option value="PINDAH_KK" {{ old('jenis_mutasi')=='PINDAH_KK'?'selected':'' }}>Pindah KK</option><option value="MENINGGAL" {{ old('jenis_mutasi')=='MENINGGAL'?'selected':'' }}>Meninggal</option></select></div>
                <div><label class="block text-sm font-semibold mb-1">Tanggal Mutasi *</label><input type="date" name="tanggal_mutasi" value="{{ old('tanggal_mutasi', date('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">KK Lama</label><select name="kk_lama_id" class="w-full px-3 py-2 border rounded-md text-sm"><option value="">--</option>@foreach($kk as $k)<option value="{{ $k->id }}" {{ old('kk_lama_id')==$k->id?'selected':'' }}>{{ $k->no_kk }} - {{ $k->kepala_keluarga }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">KK Baru</label><select name="kk_baru_id" class="w-full px-3 py-2 border rounded-md text-sm"><option value="">--</option>@foreach($kk as $k)<option value="{{ $k->id }}" {{ old('kk_baru_id')==$k->id?'selected':'' }}>{{ $k->no_kk }} - {{ $k->kepala_keluarga }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">Alamat Asal</label><input type="text" name="alamat_asal" value="{{ old('alamat_asal') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Alamat Tujuan</label><input type="text" name="alamat_tujuan" value="{{ old('alamat_tujuan') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Alasan</label><input type="text" name="alasan" value="{{ old('alasan') }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Keterangan</label><textarea name="keterangan" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('keterangan') }}</textarea></div>
            </div>
            <div class="flex justify-end gap-2 pt-4"><a href="{{ route('mutasi-warga.index') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a><button type="submit" class="px-6 py-2 bg-sp-primary text-white rounded-md text-sm font-semibold">Simpan</button></div>
        </form>
    </x-card>
</div>
@endsection
