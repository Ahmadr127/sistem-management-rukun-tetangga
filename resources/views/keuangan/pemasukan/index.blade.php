@extends('layouts.app')
@section('title', 'Pemasukan')
@section('content')
<div class="w-full mx-auto space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Total Pemasukan</div><div class="text-xl font-bold text-green-600">Rp {{ number_format($saldo['pemasukan'],0,',','.') }}</div></div>
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Total Pengeluaran</div><div class="text-xl font-bold text-red-600">Rp {{ number_format($saldo['pengeluaran'],0,',','.') }}</div></div>
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Saldo</div><div class="text-xl font-bold text-sp-primary">Rp {{ number_format($saldo['saldo'],0,',','.') }}</div></div>
    </div>
    <x-card padding="false" accent="green">
        <x-slot name="title">Transaksi Pemasukan</x-slot>
        <x-slot name="actions">
            @if(auth()->user()->hasPermission('manage_keuangan'))
            <button type="button" data-open-modal="modal-pemasukan-create" class="px-3 py-1.5 bg-green-600 text-white rounded-md text-sm font-semibold"><i class="bi bi-plus-lg"></i> Tambah Pemasukan</button>
            @endif
        </x-slot>
        <div class="px-4 py-3 border-b bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]"><label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label><input type="text" name="search" value="{{ request('search') }}" placeholder="Kategori, keterangan..." class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="w-40"><label class="block text-xs font-semibold text-gray-600 mb-1">Kategori</label><select name="kategori" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"><option value="">Semua</option>@foreach($categories as $c)<option value="{{ $c }}" {{ request('kategori')==$c?'selected':'' }}>{{ $c }}</option>@endforeach</select></div>
                <div class="w-36"><label class="block text-xs font-semibold text-gray-600 mb-1">Dari</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="w-36"><label class="block text-xs font-semibold text-gray-600 mb-1">Sampai</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="flex gap-2"><button type="submit" class="px-4 py-1.5 text-sm bg-sp-primary text-white rounded-md">Filter</button><a href="{{ route('keuangan.pemasukan.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a></div>
            </form>
        </div>
        <x-table :columns="['No','Tanggal','Kategori','Jumlah','Sumber Dana','Keterangan','Aksi']" :pagination="$keuangan" accent="green">
            @foreach($keuangan as $k)
            <tr class="hover:bg-green-50/40">
                <td class="px-3 py-2 text-sm">{{ ($keuangan->currentPage()-1)*$keuangan->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2 text-sm">{{ $k->tanggal->format('d/m/Y') }}</td>
                <td class="px-3 py-2"><span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">{{ $k->kategori }}</span></td>
                <td class="px-3 py-2 text-sm font-semibold text-green-600">Rp {{ number_format($k->jumlah,0,',','.') }}</td>
                <td class="px-3 py-2 text-sm">{{ $k->sumber_dana ?? '-' }}</td>
                <td class="px-3 py-2 text-sm max-w-[200px] truncate">{{ $k->keterangan ?? $k->deskripsi ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <x-actions>
                        @if(auth()->user()->hasPermission('manage_keuangan'))
                        <x-actions-item icon="bi-pencil" label="Edit"
                            data-open-modal="modal-pemasukan-edit"
                            data-edit-url="{{ route('keuangan.update', $k) }}"
                            data-tanggal="{{ $k->tanggal->format('Y-m-d') }}"
                            data-jenis="{{ $k->jenis }}"
                            data-kategori="{{ $k->kategori }}"
                            data-jumlah="{{ $k->jumlah }}"
                            data-sumber="{{ $k->sumber_dana }}"
                            data-keterangan="{{ $k->keterangan }}"
                            onclick="fillPemasukanEditModal(this)" />
                        <x-actions-form action="{{ route('keuangan.destroy', $k) }}" method="DELETE" icon="bi-trash" label="Hapus" confirm="Hapus transaksi ini?" />
                        @endif
                    </x-actions>
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>
</div>

{{-- Modal tambah & edit pemasukan --}}
@if(auth()->user()->hasPermission('manage_keuangan'))
<x-modal id="modal-pemasukan-create" title="Tambah Pemasukan" maxWidth="max-w-2xl">
    @include('keuangan._form', [
        'action' => route('keuangan.store'),
        'method' => 'POST',
        'modal' => 'create-pemasukan',
        'keuangan' => null,
        'categories' => $categories,
        'submitLabel' => 'Simpan',
        'submitClass' => 'bg-green-600',
        'prefix' => 'pemasukanCreate',
        'jenisDefault' => 'PEMASUKAN',
        'showJenisSelect' => false,
    ])
</x-modal>

<x-modal id="modal-pemasukan-edit" title="Edit Pemasukan" maxWidth="max-w-2xl">
    @include('keuangan._form', [
        'action' => '',
        'method' => 'PUT',
        'modal' => 'edit-pemasukan',
        'keuangan' => null,
        'categories' => $categories,
        'submitLabel' => 'Update',
        'submitClass' => 'bg-green-600',
        'prefix' => 'pemasukanEdit',
        'formId' => 'pemasukanEditForm',
        'jenisDefault' => 'PEMASUKAN',
        'showJenisSelect' => true,
    ])
</x-modal>

@push('scripts')
<script>
function fillPemasukanEditModal(btn) {
    const d = btn.dataset;
    document.getElementById('pemasukanEditForm').action = d.editUrl;
    document.getElementById('pemasukanEditModalId').value = d.editUrl.split('/').pop();
    document.getElementById('pemasukanEditTanggal').value = d.tanggal || '';
    document.getElementById('pemasukanEditJenis').value = d.jenis || 'PEMASUKAN';
    document.getElementById('pemasukanEditKategori').value = d.kategori || '';
    window.Rupiah && Rupiah.setValue('pemasukanEditJumlah', d.jumlah);
    document.getElementById('pemasukanEditSumber').value = d.sumber || '';
    document.getElementById('pemasukanEditKeterangan').value = d.keterangan || '';
}
</script>
@endpush

{{-- Buka kembali modal jika validasi gagal --}}
@if($errors->any() && old('_modal'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const which = @json(old('_modal'));
    if (which === 'create-pemasukan') {
        openModal('modal-pemasukan-create');
    } else if (which === 'edit-pemasukan') {
        const id = @json(old('_modal_id'));
        document.getElementById('pemasukanEditForm').action = '{{ url('keuangan') }}/' + id;
        document.getElementById('pemasukanEditModalId').value = id;
        document.getElementById('pemasukanEditTanggal').value = @json(old('tanggal'));
        document.getElementById('pemasukanEditJenis').value = @json(old('jenis', 'PEMASUKAN'));
        document.getElementById('pemasukanEditKategori').value = @json(old('kategori'));
        window.Rupiah && Rupiah.setValue('pemasukanEditJumlah', @json(old('jumlah')));
        document.getElementById('pemasukanEditSumber').value = @json(old('sumber_dana'));
        document.getElementById('pemasukanEditKeterangan').value = @json(old('keterangan'));
        openModal('modal-pemasukan-edit');
    }
});
</script>
@endpush
@endif
@endif
@endsection
