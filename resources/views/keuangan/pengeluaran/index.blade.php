@extends('layouts.app')
@section('title', 'Pengeluaran')
@section('content')
<div class="w-full mx-auto space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Total Pemasukan</div><div class="text-xl font-bold text-green-600">Rp {{ number_format($saldo['pemasukan'],0,',','.') }}</div></div>
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Total Pengeluaran</div><div class="text-xl font-bold text-red-600">Rp {{ number_format($saldo['pengeluaran'],0,',','.') }}</div></div>
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Saldo</div><div class="text-xl font-bold text-sp-primary">Rp {{ number_format($saldo['saldo'],0,',','.') }}</div></div>
    </div>
    <x-card padding="false" accent="red">
        <x-slot name="title">Transaksi Pengeluaran</x-slot>
        <x-slot name="actions">
            @if(auth()->user()->hasPermission('manage_keuangan'))
            <button type="button" data-open-modal="modal-pengeluaran-create" class="px-3 py-1.5 bg-red-600 text-white rounded-md text-sm font-semibold"><i class="bi bi-plus-lg"></i> Tambah Pengeluaran</button>
            @endif
        </x-slot>
        <div class="px-4 py-3 border-b bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]"><label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label><input type="text" name="search" value="{{ request('search') }}" placeholder="Kategori, keterangan..." class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="w-40"><label class="block text-xs font-semibold text-gray-600 mb-1">Kategori</label><select name="kategori" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"><option value="">Semua</option>@foreach($categories as $c)<option value="{{ $c }}" {{ request('kategori')==$c?'selected':'' }}>{{ $c }}</option>@endforeach</select></div>
                <div class="w-36"><label class="block text-xs font-semibold text-gray-600 mb-1">Dari</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="w-36"><label class="block text-xs font-semibold text-gray-600 mb-1">Sampai</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="flex gap-2"><button type="submit" class="px-4 py-1.5 text-sm bg-sp-primary text-white rounded-md">Filter</button><a href="{{ route('keuangan.pengeluaran.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a></div>
            </form>
        </div>
        <x-table :columns="['No','Tanggal','Kategori','Jumlah','Sumber Dana','Keterangan','Aksi']" :pagination="$keuangan" accent="red">
            @foreach($keuangan as $k)
            <tr class="hover:bg-red-50/40">
                <td class="px-3 py-2 text-sm">{{ ($keuangan->currentPage()-1)*$keuangan->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2 text-sm">{{ $k->tanggal->format('d/m/Y') }}</td>
                <td class="px-3 py-2"><span class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">{{ $k->kategori }}</span></td>
                <td class="px-3 py-2 text-sm font-semibold text-red-600">Rp {{ number_format($k->jumlah,0,',','.') }}</td>
                <td class="px-3 py-2 text-sm">{{ $k->sumber_dana ?? '-' }}</td>
                <td class="px-3 py-2 text-sm max-w-[200px] truncate">{{ $k->keterangan ?? $k->deskripsi ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <x-actions>
                        @if(auth()->user()->hasPermission('manage_keuangan'))
                        <x-actions-item icon="bi-pencil" label="Edit"
                            data-open-modal="modal-pengeluaran-edit"
                            data-edit-url="{{ route('keuangan.update', $k) }}"
                            data-tanggal="{{ $k->tanggal->format('Y-m-d') }}"
                            data-jenis="{{ $k->jenis }}"
                            data-kategori="{{ $k->kategori }}"
                            data-jumlah="{{ $k->jumlah }}"
                            data-sumber="{{ $k->sumber_dana }}"
                            data-keterangan="{{ $k->keterangan }}"
                            onclick="fillPengeluaranEditModal(this)" />
                        <x-actions-form action="{{ route('keuangan.destroy', $k) }}" method="DELETE" icon="bi-trash" label="Hapus" confirm="Hapus transaksi ini?" />
                        @endif
                    </x-actions>
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>
</div>

{{-- Modal tambah & edit pengeluaran --}}
@if(auth()->user()->hasPermission('manage_keuangan'))
<x-modal id="modal-pengeluaran-create" title="Tambah Pengeluaran" maxWidth="max-w-2xl">
    @include('keuangan._form', [
        'action' => route('keuangan.store'),
        'method' => 'POST',
        'modal' => 'create-pengeluaran',
        'keuangan' => null,
        'categories' => $categories,
        'submitLabel' => 'Simpan',
        'submitClass' => 'bg-red-600',
        'prefix' => 'pengeluaranCreate',
        'jenisDefault' => 'PENGELUARAN',
        'showJenisSelect' => false,
    ])
</x-modal>

<x-modal id="modal-pengeluaran-edit" title="Edit Pengeluaran" maxWidth="max-w-2xl">
    @include('keuangan._form', [
        'action' => '',
        'method' => 'PUT',
        'modal' => 'edit-pengeluaran',
        'keuangan' => null,
        'categories' => $categories,
        'submitLabel' => 'Update',
        'submitClass' => 'bg-red-600',
        'prefix' => 'pengeluaranEdit',
        'formId' => 'pengeluaranEditForm',
        'jenisDefault' => 'PENGELUARAN',
        'showJenisSelect' => true,
    ])
</x-modal>

@push('scripts')
<script>
function fillPengeluaranEditModal(btn) {
    const d = btn.dataset;
    document.getElementById('pengeluaranEditForm').action = d.editUrl;
    document.getElementById('pengeluaranEditModalId').value = d.editUrl.split('/').pop();
    document.getElementById('pengeluaranEditTanggal').value = d.tanggal || '';
    document.getElementById('pengeluaranEditJenis').value = d.jenis || 'PENGELUARAN';
    document.getElementById('pengeluaranEditKategori').value = d.kategori || '';
    window.Rupiah && Rupiah.setValue('pengeluaranEditJumlah', d.jumlah);
    document.getElementById('pengeluaranEditSumber').value = d.sumber || '';
    document.getElementById('pengeluaranEditKeterangan').value = d.keterangan || '';
}
</script>
@endpush

{{-- Buka kembali modal jika validasi gagal --}}
@if($errors->any() && old('_modal'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const which = @json(old('_modal'));
    if (which === 'create-pengeluaran') {
        openModal('modal-pengeluaran-create');
    } else if (which === 'edit-pengeluaran') {
        const id = @json(old('_modal_id'));
        document.getElementById('pengeluaranEditForm').action = '{{ url('keuangan') }}/' + id;
        document.getElementById('pengeluaranEditModalId').value = id;
        document.getElementById('pengeluaranEditTanggal').value = @json(old('tanggal'));
        document.getElementById('pengeluaranEditJenis').value = @json(old('jenis', 'PENGELUARAN'));
        document.getElementById('pengeluaranEditKategori').value = @json(old('kategori'));
        window.Rupiah && Rupiah.setValue('pengeluaranEditJumlah', @json(old('jumlah')));
        document.getElementById('pengeluaranEditSumber').value = @json(old('sumber_dana'));
        document.getElementById('pengeluaranEditKeterangan').value = @json(old('keterangan'));
        openModal('modal-pengeluaran-edit');
    }
});
</script>
@endpush
@endif
@endif
@endsection
