@extends('layouts.app')
@section('title', 'Tambah Kas Warga')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Tambah Kas Warga" subtitle="Input kas warga" accent="green">
        <form method="POST" action="{{ route('kas-warga.store') }}" class="space-y-4">
            @csrf
            @if(auth()->user()->isSuperAdmin())
            <div>
                <label class="block text-sm font-semibold mb-1">RT *</label>
                <select name="rt_id" id="rt_id" class="w-full px-3 py-2 border rounded-md text-sm" required>
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
                <label class="block text-sm font-semibold mb-1">Warga *</label>
                <select name="warga_id" id="warga_id" class="w-full px-3 py-2 border rounded-md text-sm" required>
                    <option value="">-- Pilih Warga --</option>
                    @foreach($wargas as $w)<option value="{{ $w->id }}" {{ old('warga_id')==$w->id?'selected':'' }}>{{ $w->nama }} - {{ $w->nik }}</option>@endforeach
                </select>
                @error('warga_id')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Jenis Periode *</label>
                    <select name="periode_type" class="w-full px-3 py-2 border rounded-md text-sm" required>
                        <option value="monthly" {{ old('periode_type','monthly')=='monthly'?'selected':'' }}>Bulanan</option>
                        <option value="weekly" {{ old('periode_type')=='weekly'?'selected':'' }}>Mingguan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Periode *</label>
                    <input type="text" name="periode" value="{{ old('periode', now()->format('Y-m')) }}" placeholder="2026-09" class="w-full px-3 py-2 border rounded-md text-sm" required>
                    @error('periode')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Nominal *</label>
                <input type="number" name="nominal" value="{{ old('nominal', 20000) }}" class="w-full px-3 py-2 border rounded-md text-sm" required>
                @error('nominal')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Tanggal Bayar</label>
                <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar') }}" class="w-full px-3 py-2 border rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Status *</label>
                <select name="status" class="w-full px-3 py-2 border rounded-md text-sm" required>
                    <option value="belum_bayar" {{ old('status')=='belum_bayar'?'selected':'' }}>Belum Bayar</option>
                    <option value="sudah_bayar" {{ old('status','belum_bayar')=='sudah_bayar'?'selected':'' }}>Sudah Bayar</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Catatan</label>
                <textarea name="catatan" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('catatan') }}</textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <a href="{{ route('kas-warga.index') }}" class="px-4 py-2 text-sm bg-gray-200 rounded-md">Batal</a>
                <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md font-semibold">Simpan</button>
            </div>
        </form>
    </x-card>
</div>
@push('scripts')
<script>
document.getElementById('rt_id')?.addEventListener('change', async function(){
    const rtId = this.value;
    const wargaSelect = document.getElementById('warga_id');
    wargaSelect.innerHTML = '<option value="">Loading...</option>';
    if(!rtId){ wargaSelect.innerHTML='<option value="">-- Pilih Warga --</option>'; return; }
    const res = await fetch(`{{ route('api.warga-by-rt') }}?rt_id=${rtId}`);
    const data = await res.json();
    wargaSelect.innerHTML = '<option value="">-- Pilih Warga --</option>';
    data.forEach(w=> {
        const opt = document.createElement('option');
        opt.value = w.id;
        opt.textContent = `${w.nama} - ${w.nik}`;
        wargaSelect.appendChild(opt);
    });
});
</script>
@endpush
@endsection
