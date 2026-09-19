@extends('layouts.app')
@section('title', 'Edit Kas Warga')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Edit Kas Warga" accent="green">
        <form method="POST" action="{{ route('kas-warga.update', $kasWarga) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-semibold mb-1">RT</label>
                <div class="px-3 py-2 bg-gray-50 border rounded-md text-sm">{{ $kasWarga->rt->kode_rt }}</div>
                <input type="hidden" name="rt_id" value="{{ $kasWarga->rt_id }}">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Warga *</label>
                <select name="warga_id" class="w-full px-3 py-2 border rounded-md text-sm" required>
                    @foreach($wargas as $w)<option value="{{ $w->id }}" {{ old('warga_id',$kasWarga->warga_id)==$w->id?'selected':'' }}>{{ $w->nama }} - {{ $w->nik }}</option>@endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Jenis Periode *</label>
                    <select name="periode_type" class="w-full px-3 py-2 border rounded-md text-sm">
                        <option value="monthly" {{ old('periode_type',$kasWarga->periode_type)=='monthly'?'selected':'' }}>Bulanan</option>
                        <option value="weekly" {{ old('periode_type',$kasWarga->periode_type)=='weekly'?'selected':'' }}>Mingguan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Periode *</label>
                    <input type="text" name="periode" value="{{ old('periode',$kasWarga->periode) }}" class="w-full px-3 py-2 border rounded-md text-sm" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Nominal *</label>
                <input type="number" name="nominal" value="{{ old('nominal',$kasWarga->nominal) }}" class="w-full px-3 py-2 border rounded-md text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Tanggal Bayar</label>
                <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar',$kasWarga->tanggal_bayar?->format('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Status *</label>
                <select name="status" class="w-full px-3 py-2 border rounded-md text-sm">
                    <option value="belum_bayar" {{ old('status',$kasWarga->status)=='belum_bayar'?'selected':'' }}>Belum Bayar</option>
                    <option value="sudah_bayar" {{ old('status',$kasWarga->status)=='sudah_bayar'?'selected':'' }}>Sudah Bayar</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Catatan</label>
                <textarea name="catatan" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('catatan',$kasWarga->catatan) }}</textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <a href="{{ route('kas-warga.index') }}" class="px-4 py-2 text-sm bg-gray-200 rounded-md">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md">Perbarui</button>
            </div>
        </form>
    </x-card>
</div>
@endsection
