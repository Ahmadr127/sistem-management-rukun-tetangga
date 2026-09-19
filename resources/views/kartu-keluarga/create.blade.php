@extends('layouts.app')
@section('title', 'Tambah KK')
@section('content')
<div class="w-full mx-auto">
    <x-card>
        <x-slot name="title">Tambah Kartu Keluarga</x-slot>
        <x-slot name="actions"><a href="{{ route('kartu-keluarga.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        <form action="{{ route('kartu-keluarga.store') }}" method="POST" class="space-y-4">
            @csrf
            @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                    <div class="font-semibold mb-1">Validasi gagal:</div>
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">No KK (16 digit) *</label>
                    <input type="text" name="no_kk" value="{{ old('no_kk') }}" maxlength="16" class="w-full px-3 py-2 border rounded-md text-sm @error('no_kk') border-red-300 @enderror" required>
                    @error('no_kk')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Kepala Keluarga <span class="text-gray-400 font-normal">(manual — bisa diubah setelah tambah anggota)</span></label>
                    <input type="text" name="kepala_keluarga" value="{{ old('kepala_keluarga') }}" class="w-full px-3 py-2 border rounded-md text-sm @error('kepala_keluarga') border-red-300 @enderror" placeholder="Nama kepala keluarga">
                    @error('kepala_keluarga')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    @error('kepala_keluarga_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <x-rt-address-selector :rts="$rts" :selectedRtId="old('rt_id', auth()->user()->rt_id)" :required="true" alamatName="alamat" :alamatValue="old('alamat')" />
            <p class="text-xs text-gray-500 -mt-2">Alamat jalan bebas edit. RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos otomatis dari master RT.</p>
            <div class="flex justify-end gap-2 pt-4"><a href="{{ route('kartu-keluarga.index') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a><button type="submit" class="px-6 py-2 bg-sp-primary text-white rounded-md text-sm font-semibold">Simpan</button></div>
        </form>
    </x-card>
</div>
@endsection
