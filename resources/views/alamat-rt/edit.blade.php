@extends('layouts.app')
@section('title','Edit Alamat RT')
@section('content')
<div class="max-w-3xl mx-auto">
    <x-card title="Edit Alamat RT: {{ $alamatRt->rt->kode_rt }}" accent="teal">
        <form method="POST" action="{{ route('alamat-rt.update', $alamatRt) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-semibold mb-1">RT *</label>
                <select name="rt_id" class="w-full px-3 py-2 border rounded-md text-sm" required>
                    @foreach($rts as $rt)<option value="{{ $rt->id }}" {{ old('rt_id',$alamatRt->rt_id)==$rt->id?'selected':'' }}>{{ $rt->kode_rt }} - {{ $rt->nama_rt }}</option>@endforeach
                </select>
                @error('rt_id')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2"><label class="block text-sm font-semibold mb-1">Alamat</label><input type="text" name="alamat" value="{{ old('alamat',$alamatRt->alamat) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">RW</label><input type="text" name="rw" value="{{ old('rw',$alamatRt->rw) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kelurahan</label><input type="text" name="kelurahan" value="{{ old('kelurahan',$alamatRt->kelurahan) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kecamatan</label><input type="text" name="kecamatan" value="{{ old('kecamatan',$alamatRt->kecamatan) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kota</label><input type="text" name="kota" value="{{ old('kota',$alamatRt->kota) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Provinsi</label><input type="text" name="provinsi" value="{{ old('provinsi',$alamatRt->provinsi) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kode Pos</label><input type="text" name="kode_pos" value="{{ old('kode_pos',$alamatRt->kode_pos) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
            </div>
            <label class="flex items-center gap-2"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ old('is_active',$alamatRt->is_active)?'checked':'' }} class="rounded"> <span class="text-sm">Aktif</span></label>
            <div class="flex gap-2 justify-end"><a href="{{ route('alamat-rt.index') }}" class="px-4 py-2 text-sm bg-gray-200 rounded-md">Batal</a><button type="submit" class="px-4 py-2 text-sm bg-teal-600 text-white rounded-md">Perbarui</button></div>
        </form>
    </x-card>
</div>
@endsection
