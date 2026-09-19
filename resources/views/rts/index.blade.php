@extends('layouts.app')
@section('title', 'Data RT')
@section('content')
<div class="w-full mx-auto">
    <x-card padding="false" accent="teal">
        <x-slot name="title">Data RT</x-slot>
        <x-slot name="subtitle">Pengelompokan RT — kelola RT dinamis</x-slot>
        <x-slot name="actions">
            @if(auth()->user()->hasPermission('manage_rt'))
            <a href="{{ route('rts.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white rounded-md bg-teal-600 hover:bg-teal-700 transition-colors">
                <i class="bi bi-plus-lg"></i> Tambah RT
            </a>
            @endif
        </x-slot>
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode/nama RT..." class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                </div>
                <div class="w-36">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select name="is_active" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        <option value="1" {{ request('is_active')==='1'?'selected':'' }}>Aktif</option>
                        <option value="0" {{ request('is_active')==='0'?'selected':'' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-1.5 text-sm bg-teal-600 text-white rounded-md">Filter</button>
                    <a href="{{ route('rts.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a>
                </div>
            </form>
        </div>
        <x-table :columns="['No','Kode RT','Nama RT','Jumlah Warga','Jumlah KK','Status','Aksi']" :pagination="$rts" accent="teal">
            @foreach($rts as $rt)
            <tr class="hover:bg-teal-50/40">
                <td class="px-3 py-2 text-sm">{{ ($rts->currentPage()-1)*$rts->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2"><span class="px-2 py-0.5 text-xs font-semibold rounded bg-teal-100 text-teal-800">{{ $rt->kode_rt }}</span></td>
                <td class="px-3 py-2 text-sm font-medium">{{ $rt->nama_rt }}</td>
                <td class="px-3 py-2 text-sm text-center">{{ $rt->warga_count }}</td>
                <td class="px-3 py-2 text-sm text-center">{{ $rt->kartu_keluarga_count }}</td>
                <td class="px-3 py-2">
                    @if($rt->is_active)<span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">Aktif</span>
                    @else<span class="px-2 py-0.5 text-xs rounded-full bg-gray-200 text-gray-700">Nonaktif</span>@endif
                </td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <x-actions>
                        <x-actions-item href="{{ route('rts.show', $rt) }}" icon="bi-eye" label="Lihat" />
                        @if(auth()->user()->hasPermission('manage_rt'))
                        <x-actions-item href="{{ route('rts.edit', $rt) }}" icon="bi-pencil" label="Edit" />
                        <x-actions-form action="{{ route('rts.destroy', $rt) }}" method="DELETE" icon="bi-trash" label="Hapus" confirm="Yakin hapus RT ini?" />
                        @endif
                    </x-actions>
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>
</div>
@endsection
