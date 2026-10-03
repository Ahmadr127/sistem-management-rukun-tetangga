@extends('layouts.app')
@section('title', 'Dana Lingkungan')
@section('content')
<div class="w-full mx-auto space-y-4">
    <x-card padding="false" accent="green">
        <x-slot name="title">Dana Lingkungan @if(auth()->user()->rt) {{ auth()->user()->rt->kode_rt }} @endif</x-slot>
        <x-slot name="subtitle">Kelola jenis kas (bulanan / tahunan / mingguan) per KK atau perorangan — klik Buka untuk input pembayaran harian</x-slot>
        <x-slot name="actions">
            <div class="flex gap-2">
                @if(auth()->user()->hasPermission('manage_kas'))
                <button type="button" data-open-modal="modal-kas-create" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white rounded-md bg-green-600 hover:bg-green-700">
                    <i class="bi bi-plus-lg"></i> Tambah Jenis Kas
                </button>
                @endif
            </div>
        </x-slot>
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Jenis Kas</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama kas..." class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                </div>
                @if(auth()->user()->isSuperAdmin())
                <div class="w-32">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">RT</label>
                    <select name="rt_id" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        @foreach($rts as $rt)<option value="{{ $rt->id }}" {{ (string)request('rt_id')===(string)$rt->id?'selected':'' }}>{{ $rt->kode_rt }}</option>@endforeach
                    </select>
                </div>
                @endif
                <div class="w-32">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Periode</label>
                    <select name="periode_type" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        <option value="weekly" {{ request('periode_type')=='weekly'?'selected':'' }}>Mingguan</option>
                        <option value="monthly" {{ request('periode_type')=='monthly'?'selected':'' }}>Bulanan</option>
                        <option value="yearly" {{ request('periode_type')=='yearly'?'selected':'' }}>Tahunan</option>
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Target</label>
                    <select name="target_type" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        <option value="kk" {{ request('target_type')=='kk'?'selected':'' }}>KK</option>
                        <option value="perorangan" {{ request('target_type')=='perorangan'?'selected':'' }}>Perorangan</option>
                    </select>
                </div>
                <div class="w-28">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select name="is_active" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        <option value="1" {{ request('is_active')==='1'?'selected':'' }}>Aktif</option>
                        <option value="0" {{ request('is_active')==='0'?'selected':'' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-1.5 text-sm bg-green-600 text-white rounded-md">Filter</button>
                    <a href="{{ route('kas-warga.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a>
                </div>
            </form>
        </div>
        @if($jenis->count())
        <div class="flex flex-wrap justify-start gap-x-1 gap-y-3 p-2">
            @foreach($jenis as $j)
            <div class="group relative w-40 shrink-0 p-1 flex flex-col items-center text-center transition-all duration-200 hover:-translate-y-1.5 {{ $j->is_active ? '' : 'opacity-60' }}">
                @if(auth()->user()->hasPermission('manage_kas'))
                <div class="absolute top-0 right-0 flex gap-1 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                    <button type="button" title="Edit" data-open-modal="modal-kas-edit"
                       data-edit-url="{{ route('kas-warga.update', $j) }}"
                       data-rt-id="{{ $j->rt_id }}"
                       data-rt-name="{{ $j->rt?->kode_rt }}"
                       data-nama="{{ $j->nama }}"
                       data-periode="{{ $j->periode_type }}"
                       data-target="{{ $j->target_type }}"
                       data-nominal="{{ $j->nominal }}"
                       data-active="{{ $j->is_active ? '1' : '0' }}"
                       data-deskripsi="{{ $j->deskripsi }}"
                       onclick="fillKasEditModal(this)"
                       class="w-6 h-6 inline-flex items-center justify-center rounded-md bg-white border border-gray-200 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 shadow-sm">
                        <i class="bi bi-pencil text-[10px]"></i>
                    </button>
                    <form action="{{ route('kas-warga.destroy', $j) }}" method="POST" class="inline"
                          onsubmit="return confirm('Yakin hapus jenis kas ini beserta SEMUA data pembayarannya?')">
                        @csrf @method('DELETE')
                        <button type="submit" title="Hapus"
                                class="w-6 h-6 inline-flex items-center justify-center rounded-md bg-white border border-gray-200 text-slate-400 hover:text-red-600 hover:border-red-300 hover:bg-red-50 shadow-sm">
                            <i class="bi bi-trash text-[10px]"></i>
                        </button>
                    </form>
                </div>
                @endif

                <a href="{{ route('kas-warga.show', $j) }}" title="Buka {{ $j->nama }}"
                   class="flex flex-col items-center w-full">
                    <span class="relative block w-[72px] h-[72px] transition-transform duration-300 ease-out group-hover:scale-105">
                        {{-- Folder tertutup (state normal) --}}
                        <img src="{{ asset('images/folder.png') }}" alt="Arsip tertutup"
                             class="absolute inset-0 w-full h-full object-contain transition-all duration-300 ease-out group-hover:opacity-0 group-hover:scale-95">
                        {{-- Folder terbuka (state hover) --}}
                        <img src="{{ asset('images/open-folder.png') }}" alt="Arsip terbuka"
                             class="absolute inset-0 w-full h-full object-contain opacity-0 scale-95 transition-all duration-300 ease-out group-hover:opacity-100 group-hover:scale-100">
                    </span>
                    <span class="mt-1 text-xs font-semibold text-slate-700 leading-tight line-clamp-2">{{ $j->nama }}</span>
                </a>
            </div>
            @endforeach
        </div>
        <div class="px-4 pb-4">
            {{ $jenis->links() }}
        </div>
        @else
        <div class="p-10 text-center">
            <i class="bi bi-journal-bookmark text-5xl text-gray-200"></i>
            <p class="mt-3 text-sm font-semibold text-gray-600">Belum ada jenis kas</p>
            <p class="mt-1 text-xs text-gray-400">Klik <b>Tambah Jenis Kas</b> untuk membuat (mis. Kas Bulanan Rp20.000 / KK).</p>
        </div>
        @endif
    </x-card>
</div>

{{-- Modal tambah jenis kas --}}
@if(auth()->user()->hasPermission('manage_kas'))
<x-modal id="modal-kas-create" title="Tambah Jenis Kas" maxWidth="max-w-2xl">
    @include('kas-warga._form', [
        'action' => route('kas-warga.store'),
        'method' => 'POST',
        'modal' => 'create-kas',
        'kasJenis' => null,
        'rts' => $rts,
        'submitLabel' => 'Simpan & Buka Tabel',
        'prefix' => 'kasCreate',
    ])
</x-modal>

{{-- Modal edit jenis kas (diisi via JS) --}}
<x-modal id="modal-kas-edit" title="Edit Jenis Kas" maxWidth="max-w-2xl">
    @include('kas-warga._form', [
        'action' => '',
        'method' => 'PUT',
        'modal' => 'edit-kas',
        'kasJenis' => null,
        'rts' => $rts,
        'submitLabel' => 'Perbarui',
        'prefix' => 'kasEdit',
        'formId' => 'kasEditForm',
    ])
</x-modal>

@push('scripts')
<script>
function fillKasEditModal(btn) {
    const d = btn.dataset;
    document.getElementById('kasEditForm').action = d.editUrl;
    document.getElementById('kasEditModalId').value = d.editUrl.split('/').pop();
    const rt = document.getElementById('kasEditRt');
    if (rt) rt.value = d.rtId;
    const rtHidden = document.getElementById('kasEditRtHidden');
    if (rtHidden) rtHidden.value = d.rtId;
    const rtName = document.getElementById('kasEditRtName');
    if (rtName && d.rtName) rtName.textContent = d.rtName;
    document.getElementById('kasEditNama').value = d.nama || '';
    document.getElementById('kasEditPeriode').value = d.periode || 'monthly';
    document.getElementById('kasEditTarget').value = d.target || 'kk';
    window.Rupiah && Rupiah.setValue('kasEditNominal', d.nominal);
    document.getElementById('kasEditActive').checked = d.active === '1';
    document.getElementById('kasEditDeskripsi').value = d.deskripsi || '';
}
</script>
@endpush

{{-- Buka kembali modal jika validasi gagal --}}
@if($errors->any() && old('_modal'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const which = @json(old('_modal'));
    if (which === 'create-kas') {
        openModal('modal-kas-create');
    } else if (which === 'edit-kas') {
        const id = @json(old('_modal_id'));
        document.getElementById('kasEditForm').action = '{{ url('kas-warga') }}/' + id;
        document.getElementById('kasEditModalId').value = id;
        const rt = document.getElementById('kasEditRt');
        if (rt) rt.value = @json(old('rt_id'));
        const rtHidden = document.getElementById('kasEditRtHidden');
        if (rtHidden) rtHidden.value = @json(old('rt_id'));
        document.getElementById('kasEditNama').value = @json(old('nama'));
        document.getElementById('kasEditPeriode').value = @json(old('periode_type', 'monthly'));
        document.getElementById('kasEditTarget').value = @json(old('target_type', 'kk'));
        window.Rupiah && Rupiah.setValue('kasEditNominal', @json(old('nominal')));
        document.getElementById('kasEditActive').checked = @json((bool) old('is_active'));
        document.getElementById('kasEditDeskripsi').value = @json(old('deskripsi'));
        openModal('modal-kas-edit');
    }
});
</script>
@endpush
@endif
@endif
@endsection
