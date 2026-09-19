@extends('layouts.app')
@section('title', 'Edit KK')
@section('content')
<div class="w-full mx-auto">
    <x-card>
        <x-slot name="title">Edit KK: {{ $kartuKeluarga->no_kk }}</x-slot>
        <x-slot name="actions"><a href="{{ route('kartu-keluarga.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a> <a href="{{ route('kartu-keluarga.show', $kartuKeluarga) }}" class="text-sm px-3 py-1.5 bg-blue-100 text-blue-800 rounded-md">Lihat Detail</a></x-slot>
        @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                <div class="font-semibold mb-1">Validasi gagal:</div>
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('kartu-keluarga.update', $kartuKeluarga) }}" method="POST" class="space-y-4" x-data="{ kepalaId: '{{ old('kepala_keluarga_id', $kartuKeluarga->warga->firstWhere('nama', $kartuKeluarga->kepala_keluarga)?->id ?? '') }}' }">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">No KK *</label>
                    <input type="text" name="no_kk" value="{{ old('no_kk',$kartuKeluarga->no_kk) }}" class="w-full px-3 py-2 border rounded-md text-sm @error('no_kk') border-red-300 @enderror" required>
                    @error('no_kk')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Kepala Keluarga <span class="text-gray-400 font-normal">(select / manual)</span></label>
                    @if($kartuKeluarga->warga->count() > 0)
                        <select name="kepala_keluarga_id" x-model="kepalaId" class="w-full px-3 py-2 border rounded-md text-sm bg-white">
                            <option value="">-- Pilih dari Anggota --</option>
                            @foreach($kartuKeluarga->warga as $w)
                                <option value="{{ $w->id }}" {{ (string)old('kepala_keluarga_id', $kartuKeluarga->warga->firstWhere('nama', $kartuKeluarga->kepala_keluarga)?->id ?? '') === (string)$w->id ? 'selected' : '' }}>{{ $w->nama }} — {{ $w->nik }} ({{ $w->hubungan_keluarga ?? '-' }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih anggota, otomatis set nama kepala. Kosongkan untuk manual.</p>
                        <input type="text" name="kepala_keluarga" value="{{ old('kepala_keluarga',$kartuKeluarga->kepala_keluarga) }}" placeholder="Atau ketik manual jika belum ada anggota" class="w-full mt-2 px-3 py-2 border rounded-md text-sm" :disabled="kepalaId !== ''" :class="kepalaId !== '' ? 'bg-gray-100 text-gray-500' : 'bg-white'">
                        @error('kepala_keluarga')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        @error('kepala_keluarga_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    @else
                        <input type="text" name="kepala_keluarga" value="{{ old('kepala_keluarga',$kartuKeluarga->kepala_keluarga) }}" class="w-full px-3 py-2 border rounded-md text-sm" placeholder="Nama kepala keluarga">
                        <p class="text-xs text-gray-500 mt-1">Belum ada anggota — isi manual. Setelah ada anggota bisa pilih via select.</p>
                    @endif
                </div>
            </div>
            <x-rt-address-selector :rts="$rts" :selectedRtId="old('rt_id', $kartuKeluarga->rt_id)" :required="true" alamatName="alamat" :alamatValue="old('alamat', $kartuKeluarga->alamat)" />
            <p class="text-xs text-gray-500 -mt-2">Alamat jalan bebas edit. RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos otomatis dari master RT.</p>

            @if($kartuKeluarga->warga->count())
            <div class="p-3 bg-gray-50 border rounded-md">
                <div class="text-sm font-semibold mb-2">Anggota Saat Ini ({{ $kartuKeluarga->warga->count() }}) — kepala ter-highlight</div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead><tr class="text-left text-gray-500"><th class="py-1">NIK</th><th class="py-1">Nama</th><th class="py-1">Hubungan</th><th class="py-1"></th></tr></thead>
                        <tbody>
                            @foreach($kartuKeluarga->warga as $w)
                            <tr class="{{ $w->nama === $kartuKeluarga->kepala_keluarga ? 'bg-amber-100 font-semibold' : '' }}"><td class="py-1 font-mono">{{ $w->nik }}</td><td class="py-1">{{ $w->nama }} @if($w->nama === $kartuKeluarga->kepala_keluarga)<span class="ml-1 px-1.5 py-0.5 bg-amber-200 text-amber-800 rounded text-xs">Kepala</span>@endif</td><td class="py-1">{{ $w->hubungan_keluarga ?? '-' }}</td><td class="py-1"><a href="{{ route('warga.show', $w) }}" class="underline text-blue-600">Lihat</a></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="flex justify-end gap-2 pt-4"><a href="{{ route('kartu-keluarga.index') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a><button type="submit" class="px-6 py-2 bg-sp-primary text-white rounded-md text-sm font-semibold">Update</button></div>
        </form>
    </x-card>
</div>
@endsection
