@extends('layouts.app')
@section('title', 'Tambah Jenis Kas')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Tambah Jenis Kas" subtitle="Input bulanan / tahunan / mingguan, nominal, dan target KK atau perorangan" accent="green">
        <form method="POST" action="{{ route('kas-warga.store') }}" class="space-y-4">
            @csrf
            @if(auth()->user()->isSuperAdmin())
            <div>
                <label class="block text-sm font-semibold mb-1">RT *</label>
                <select name="rt_id" class="w-full px-3 py-2 border rounded-md text-sm" required>
                    <option value="">-- Pilih RT --</option>
                    @foreach($rts as $rt)<option value="{{ $rt->id }}" {{ (string)old('rt_id', $selectedRt)===(string)$rt->id?'selected':'' }}>{{ $rt->kode_rt }} - {{ $rt->nama_rt }}</option>@endforeach
                </select>
                @error('rt_id')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            @else
            <div>
                <label class="block text-sm font-semibold mb-1">RT</label>
                <div class="px-3 py-2 bg-green-50 border rounded-md text-sm font-semibold text-green-800">{{ auth()->user()->rt->kode_rt }}</div>
                <input type="hidden" name="rt_id" value="{{ auth()->user()->rt_id }}">
            </div>
            @endif
            <div>
                <label class="block text-sm font-semibold mb-1">Nama Jenis Kas *</label>
                <input type="text" name="nama" value="{{ old('nama') }}" placeholder="cth: Kas Ronda Bulanan" class="w-full px-3 py-2 border rounded-md text-sm" required>
                @error('nama')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Periode *</label>
                    <select name="periode_type" class="w-full px-3 py-2 border rounded-md text-sm" required>
                        <option value="monthly" {{ old('periode_type','monthly')=='monthly'?'selected':'' }}>Bulanan</option>
                        <option value="yearly" {{ old('periode_type')=='yearly'?'selected':'' }}>Tahunan</option>
                        <option value="weekly" {{ old('periode_type')=='weekly'?'selected':'' }}>Mingguan</option>
                    </select>
                    @error('periode_type')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Target Iuran *</label>
                    <select name="target_type" class="w-full px-3 py-2 border rounded-md text-sm" required>
                        <option value="kk" {{ old('target_type','kk')=='kk'?'selected':'' }}>Per KK (Kepala Keluarga)</option>
                        <option value="perorangan" {{ old('target_type')=='perorangan'?'selected':'' }}>Perorangan (Per Warga)</option>
                    </select>
                    @error('target_type')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Nominal (Rp) *</label>
                    <input type="number" name="nominal" value="{{ old('nominal', 20000) }}" min="0" class="w-full px-3 py-2 border rounded-md text-sm" required>
                    @error('nominal')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                    <p class="text-xs text-gray-500 mt-1">Nominal per sel tanggal yang dibayar.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Status</label>
                    <label class="flex items-center gap-2 px-3 py-2 border rounded-md text-sm cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="accent-green-600">
                        Aktif (bisa dibayar)
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="2" placeholder="Keterangan tambahan (opsional)" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <a href="{{ route('kas-warga.index') }}" class="px-4 py-2 text-sm bg-gray-200 rounded-md">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md font-semibold">Simpan & Buka Tabel</button>
            </div>
        </form>
    </x-card>
</div>
@endsection
