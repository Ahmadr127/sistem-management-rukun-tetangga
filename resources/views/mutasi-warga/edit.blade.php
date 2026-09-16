@extends('layouts.app')
@section('title', 'Edit Mutasi')
@section('content')
<div class="max-w-3xl mx-auto">
    <x-card>
        <x-slot name="title">Edit Mutasi</x-slot>
        <x-slot name="actions"><a href="{{ route('mutasi-warga.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        <form action="{{ route('mutasi-warga.update', $mutasiWarga) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold mb-1">Warga *</label><select name="warga_id" class="w-full px-3 py-2 border rounded-md text-sm" required>@foreach($warga as $w)<option value="{{ $w->id }}" {{ old('warga_id',$mutasiWarga->warga_id)==$w->id?'selected':'' }}>{{ $w->nama }} - {{ $w->nik }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">Jenis *</label><select name="jenis_mutasi" class="w-full px-3 py-2 border rounded-md text-sm" required>@foreach(['LAHIR','MASUK','KELUAR','PINDAH_KK','MENINGGAL'] as $j)<option value="{{ $j }}" {{ old('jenis_mutasi',$mutasiWarga->jenis_mutasi)==$j?'selected':'' }}>{{ $j }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">Tanggal *</label><input type="date" name="tanggal_mutasi" value="{{ old('tanggal_mutasi',$mutasiWarga->tanggal_mutasi->format('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">KK Lama</label><select name="kk_lama_id" class="w-full px-3 py-2 border rounded-md text-sm"><option value="">--</option>@foreach($kk as $k)<option value="{{ $k->id }}" {{ old('kk_lama_id',$mutasiWarga->kk_lama_id)==$k->id?'selected':'' }}>{{ $k->no_kk }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">KK Baru</label><select name="kk_baru_id" class="w-full px-3 py-2 border rounded-md text-sm"><option value="">--</option>@foreach($kk as $k)<option value="{{ $k->id }}" {{ old('kk_baru_id',$mutasiWarga->kk_baru_id)==$k->id?'selected':'' }}>{{ $k->no_kk }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">Alamat Asal</label><input type="text" name="alamat_asal" value="{{ old('alamat_asal',$mutasiWarga->alamat_asal) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Alamat Tujuan</label><input type="text" name="alamat_tujuan" value="{{ old('alamat_tujuan',$mutasiWarga->alamat_tujuan) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Alasan</label><input type="text" name="alasan" value="{{ old('alasan',$mutasiWarga->alasan) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Keterangan</label><textarea name="keterangan" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('keterangan',$mutasiWarga->keterangan) }}</textarea></div>
            </div>
            <div class="flex justify-end gap-2 pt-4"><a href="{{ route('mutasi-warga.index') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a><button type="submit" class="px-6 py-2 bg-sp-primary text-white rounded-md text-sm font-semibold">Update</button></div>
        </form>
    </x-card>
</div>
@endsection
