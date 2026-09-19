@extends('layouts.app')
@section('title','Tambah Alamat RT')
@section('content')
<div class="max-w-3xl mx-auto">
    <x-card title="Tambah Alamat RT" subtitle="Master alamat wilayah" accent="teal">
        <form method="POST" action="{{ route('alamat-rt.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1">RT *</label>
                <select name="rt_id" class="w-full px-3 py-2 border rounded-md text-sm" required>
                    <option value="">-- Pilih RT --</option>
                    @foreach($rts as $rt)<option value="{{ $rt->id }}" {{ old('rt_id')==$rt->id?'selected':'' }}>{{ $rt->kode_rt }} - {{ $rt->nama_rt }}</option>@endforeach
                </select>
                @error('rt_id')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2"><label class="block text-sm font-semibold mb-1">Alamat (Jalan)</label><input type="text" name="alamat" value="{{ old('alamat') }}" placeholder="Jl. Raya Bogor" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">RW</label><input type="text" name="rw" value="{{ old('rw') }}" placeholder="05" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kelurahan/Desa</label><input type="text" name="kelurahan" value="{{ old('kelurahan') }}" placeholder="Sukamaju" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kecamatan</label><input type="text" name="kecamatan" value="{{ old('kecamatan') }}" placeholder="Bogor Timur" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kota/Kabupaten</label><input type="text" name="kota" value="{{ old('kota') }}" placeholder="Bogor" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Provinsi</label><input type="text" name="provinsi" value="{{ old('provinsi') }}" placeholder="Jawa Barat" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kode Pos</label><input type="text" name="kode_pos" value="{{ old('kode_pos') }}" placeholder="16143" class="w-full px-3 py-2 border rounded-md text-sm"></div>
            </div>
            <label class="flex items-center gap-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ old('is_active',1)?'checked':'' }} class="rounded border-gray-300 text-teal-600"> <span class="text-sm">Aktif</span></label>
            <div class="flex gap-2 justify-end"><a href="{{ route('alamat-rt.index') }}" class="px-4 py-2 text-sm bg-gray-200 rounded-md">Batal</a><button type="submit" class="px-4 py-2 text-sm bg-teal-600 text-white rounded-md font-semibold">Simpan</button></div>
        </form>
    </x-card>
</div>
@endsection
