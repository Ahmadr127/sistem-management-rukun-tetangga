@extends('layouts.app')
@section('title', 'Data Warga')
@section('content')
<div class="w-full mx-auto">
    <x-card padding="false" accent="teal">
        <x-slot name="title">Data Warga</x-slot>
        <x-slot name="subtitle">Pendataan warga — filter RT/RW dan pencarian</x-slot>
        <x-slot name="actions">
            <div class="flex items-center gap-2">
                @include('warga.partials._modal', [
                    'rts' => $rtList ?? null,
                    'action' => route('warga.store'),
                    'method' => 'POST',
                    'warga' => null,
                    'modalId' => 'addWargaIndexModal',
                    'title' => 'Tambah Warga (Modal)',
                    'trigger' => '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white rounded-md bg-teal-600 hover:bg-teal-700 transition-colors cursor-pointer"><i class="bi bi-plus-circle"></i> Modal</span>'
                ])
                <a href="{{ route('warga.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white rounded-md bg-sp-primary hover:bg-sp-primary-dark transition-colors">
                    <i class="bi bi-person-plus"></i> Tambah Warga
                </a>
            </div>
        </x-slot>
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, pekerjaan..." class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                </div>
                @if(auth()->user()->isSuperAdmin())
                <div class="w-36">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">RT</label>
                    <select name="rt_id" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua RT</option>
                        @foreach($rtList as $rt)<option value="{{ $rt->id }}" {{ (string)request('rt_id')===(string)$rt->id?'selected':'' }}>{{ $rt->kode_rt }}</option>@endforeach
                    </select>
                </div>
                @else
                <div class="w-36">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">RT</label>
                    <div class="px-2 py-1.5 text-sm bg-teal-50 border border-teal-200 rounded-md font-semibold text-teal-800 text-center">{{ auth()->user()->rt?->kode_rt ?? '-' }}</div>
                </div>
                @endif
                <div class="w-28">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">RW</label>
                    <select name="rw" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        @foreach($rwList as $rw)<option value="{{ $rw }}" {{ request('rw')==$rw?'selected':'' }}>{{ $rw }}</option>@endforeach
                    </select>
                </div>
                <div class="w-36">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select name="status_warga" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        <option value="AKTIF" {{ request('status_warga')=='AKTIF'?'selected':'' }}>Aktif</option>
                        <option value="PINDAH" {{ request('status_warga')=='PINDAH'?'selected':'' }}>Pindah</option>
                        <option value="MENINGGAL" {{ request('status_warga')=='MENINGGAL'?'selected':'' }}>Meninggal</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-1.5 text-sm bg-sp-primary text-white rounded-md">Filter</button>
                    <a href="{{ route('warga.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a>
                </div>
            </form>
        </div>
        <x-table :columns="['No','NIK','Nama','L/P','KK','RT/RW','Pekerjaan','Status','Aksi']" :pagination="$warga" accent="teal">
            @foreach($warga as $w)
            <tr class="hover:bg-teal-50/30">
                <td class="px-3 py-2 text-sm">{{ ($warga->currentPage()-1)*$warga->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2 text-sm font-mono">{{ $w->nik }}</td>
                <td class="px-3 py-2">
                    <div class="text-sm font-medium text-gray-900">{{ $w->nama }}</div>
                    <div class="text-xs text-gray-500">{{ $w->tempat_lahir }}{{ $w->tanggal_lahir ? ', '.$w->tanggal_lahir->format('d/m/Y') : '' }} @if($w->umur) ({{ $w->umur }} th) @endif</div>
                </td>
                <td class="px-3 py-2 text-sm text-center">{{ $w->jenis_kelamin }}</td>
                <td class="px-3 py-2 text-sm">{{ $w->kartuKeluarga->no_kk ?? '-' }}<div class="text-xs text-gray-500">{{ $w->hubungan_keluarga ?? '-' }}</div></td>
                <td class="px-3 py-2 text-sm text-center"><span class="px-1.5 py-0.5 text-xs bg-teal-100 text-teal-800 rounded">{{ $w->rt?->kode_rt ?? ($w->kartuKeluarga->rt ?? '-') }}</span>/{{ $w->kartuKeluarga->rw ?? '-' }}</td>
                <td class="px-3 py-2 text-sm">{{ $w->pekerjaan ?? '-' }}</td>
                <td class="px-3 py-2">
                    @if($w->status_warga=='AKTIF')<span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">Aktif</span>
                    @elseif($w->status_warga=='PINDAH')<span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">Pindah</span>
                    @else<span class="px-2 py-0.5 text-xs rounded-full bg-gray-200 text-gray-700">Meninggal</span>@endif
                </td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <x-actions>
                        <x-actions-item href="{{ route('warga.show', $w) }}" icon="bi-eye" label="Lihat" />
                        <x-actions-item href="{{ route('warga.edit', $w) }}" icon="bi-pencil" label="Edit" />
                        <x-actions-form action="{{ route('warga.destroy', $w) }}" method="DELETE" icon="bi-trash" label="Hapus" confirm="Yakin hapus warga ini?" />
                    </x-actions>
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>
</div>
@endsection
