@extends('layouts.app')
@section('title', 'Edit Jenis Kas')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Edit Jenis Kas" subtitle="{{ $kasJenis->nama }}" accent="green">
        <form method="POST" action="{{ route('kas-warga.update', $kasJenis) }}" class="space-y-4">
            @csrf @method('PUT')
            @if(auth()->user()->isSuperAdmin())
            <div>
                <label class="block text-sm font-semibold mb-1">RT *</label>
                <select name="rt_id" class="w-full px-3 py-2 border rounded-md text-sm" required>
                    @foreach($rts as $rt)<option value="{{ $rt->id }}" {{ (string)old('rt_id',$kasJenis->rt_id)===(string)$rt->id?'selected':'' }}>{{ $rt->kode_rt }} - {{ $rt->nama_rt }}</option>@endforeach
                </select>
            </div>
            @else
            <div>
                <label class="block text-sm font-semibold mb-1">RT</label>
                <div class="px-3 py-2 bg-gray-50 border rounded-md text-sm">{{ $kasJenis->rt->kode_rt }}</div>
                <input type="hidden" name="rt_id" value="{{ $kasJenis->rt_id }}">
            </div>
            @endif
            <div>
                <label class="block text-sm font-semibold mb-1">Nama Jenis Kas *</label>
                <input type="text" name="nama" value="{{ old('nama',$kasJenis->nama) }}" class="w-full px-3 py-2 border rounded-md text-sm" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Periode *</label>
                    <select name="periode_type" class="w-full px-3 py-2 border rounded-md text-sm" required>
                        <option value="monthly" {{ old('periode_type',$kasJenis->periode_type)=='monthly'?'selected':'' }}>Bulanan</option>
                        <option value="yearly" {{ old('periode_type',$kasJenis->periode_type)=='yearly'?'selected':'' }}>Tahunan</option>
                        <option value="weekly" {{ old('periode_type',$kasJenis->periode_type)=='weekly'?'selected':'' }}>Mingguan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Target Iuran *</label>
                    <select name="target_type" class="w-full px-3 py-2 border rounded-md text-sm" required>
                        <option value="kk" {{ old('target_type',$kasJenis->target_type)=='kk'?'selected':'' }}>Per KK (Kepala Keluarga)</option>
                        <option value="perorangan" {{ old('target_type',$kasJenis->target_type)=='perorangan'?'selected':'' }}>Perorangan (Per Warga)</option>
                    </select>
                    <p class="text-xs text-amber-600 mt-1">Mengganti target akan membuat data pembayaran lama tidak tampil di tabel.</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Nominal (Rp) *</label>
                    <input type="number" name="nominal" value="{{ old('nominal',$kasJenis->nominal) }}" min="0" class="w-full px-3 py-2 border rounded-md text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Status</label>
                    <label class="flex items-center gap-2 px-3 py-2 border rounded-md text-sm cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active',$kasJenis->is_active) ? 'checked' : '' }} class="accent-green-600">
                        Aktif (bisa dibayar)
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('deskripsi',$kasJenis->deskripsi) }}</textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <a href="{{ route('kas-warga.show', $kasJenis) }}" class="px-4 py-2 text-sm bg-gray-200 rounded-md">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md font-semibold">Perbarui</button>
            </div>
        </form>
    </x-card>
</div>
@endsection
