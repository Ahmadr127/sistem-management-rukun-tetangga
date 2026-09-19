@extends('layouts.app')
@section('title', 'Tambah RT')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Tambah RT" subtitle="Buat RT baru secara dinamis" accent="teal">
        <form method="POST" action="{{ route('rts.store') }}" class="space-y-4">
            @csrf
            <x-input name="kode_rt" label="Kode RT" placeholder="Contoh: RT 01" required :value="old('kode_rt')" accent="teal" />
            <x-input name="nama_rt" label="Nama RT" placeholder="Contoh: RT 01" required :value="old('nama_rt')" accent="teal" />
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-gray-400 font-normal">(opsional)</span></label>
                <textarea name="keterangan" rows="3" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">{{ old('keterangan') }}</textarea>
                @error('keterangan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active',1)?'checked':'' }} class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                <span class="text-sm text-gray-700">Aktif</span>
            </label>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-teal-600 rounded-md hover:bg-teal-700">Simpan</button>
                <a href="{{ route('rts.index') }}" class="px-4 py-2 text-sm bg-gray-200 rounded-md">Batal</a>
            </div>
        </form>
    </x-card>
</div>
@endsection
