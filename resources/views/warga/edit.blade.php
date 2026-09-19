@extends('layouts.app')
@section('title', 'Edit Warga')
@section('content')
<div class="w-full mx-auto">
    <x-card>
        <x-slot name="title">Edit Warga: {{ $warga->nama }}</x-slot>
        <x-slot name="actions"><a href="{{ route('warga.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a> <a href="{{ route('warga.show', $warga) }}" class="text-sm px-3 py-1.5 bg-blue-100 text-blue-800 rounded-md">Lihat Detail</a></x-slot>
        <form action="{{ route('warga.update', $warga) }}" method="POST" enctype="multipart/form-data" class="space-y-4" x-data>
            @csrf @method('PUT')
            <x-rt-address-selector :rts="$rts" :selectedRtId="old('rt_id', $warga->rt_id)" :required="true" alamatName="alamat_detail" :alamatValue="old('alamat_detail', $warga->alamat_detail)" />
            <div>
                <label class="block text-sm font-semibold mb-1">Kartu Keluarga</label>
                <select name="kartu_keluarga_id" id="kk_select" class="w-full px-3 py-2 border rounded-md text-sm">
                    <option value="">-- Tidak ada --</option>
                    @foreach($kkList as $kk)<option value="{{ $kk->id }}" data-rt="{{ $kk->rt_id }}" {{ old('kartu_keluarga_id',$warga->kartu_keluarga_id)==$kk->id?'selected':'' }}>{{ $kk->no_kk }} - {{ $kk->kepala_keluarga }} ({{ $kk->rtRelation?->kode_rt ?? 'RT '.$kk->rt }}/RW {{ $kk->rw }})</option>@endforeach
                </select>
                @error('kartu_keluarga_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            @include('warga.partials._fields', ['warga' => $warga])
            <div class="flex justify-end gap-2 pt-4">
                <a href="{{ route('warga.index') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a>
                <button type="submit" class="px-6 py-2 bg-sp-primary text-white rounded-md text-sm font-semibold">Update</button>
            </div>
        </form>
    </x-card>
</div>
@push('scripts')
<script>
document.addEventListener('rt-changed', e => {
    const rtId = e.detail?.rtId;
    const sel = document.getElementById('kk_select');
    if(!sel) return;
    Array.from(sel.options).forEach(opt=>{
        if(!opt.value) return;
        const optRt = opt.getAttribute('data-rt');
        if(!rtId) { opt.hidden=false; opt.disabled=false; }
        else if(optRt && String(optRt) !== String(rtId)) { opt.hidden=true; opt.disabled=true; if(opt.selected) sel.value=''; }
        else { opt.hidden=false; opt.disabled=false; }
    });
});
window.addEventListener('DOMContentLoaded', ()=>{
    document.dispatchEvent(new CustomEvent('rt-changed', {detail:{rtId: document.querySelector('[name=rt_id]')?.value}}));
});
</script>
@endpush
@endsection
