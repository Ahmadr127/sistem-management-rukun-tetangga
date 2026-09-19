@extends('layouts.app')
@section('title','Management Alamat RT')
@section('content')
<div class="w-full mx-auto">
    <x-card padding="false" accent="teal">
        <x-slot name="title">Management Alamat RT</x-slot>
        <x-slot name="subtitle">Master alamat wilayah per RT</x-slot>
        <x-slot name="actions">
            @if(auth()->user()->hasPermission('manage_alamat_rt'))
            <a href="{{ route('alamat-rt.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white rounded-md bg-teal-600 hover:bg-teal-700">
                <i class="bi bi-geo-alt"></i> Tambah Alamat
            </a>
            @endif
        </x-slot>
        <div class="px-4 py-3 border-b bg-gray-50">
            <form method="GET" class="flex gap-3 items-end">
                <div class="flex-1"><label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label><input type="text" name="search" value="{{ request('search') }}" placeholder="RT, alamat, kelurahan..." class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <button type="submit" class="px-4 py-1.5 text-sm bg-teal-600 text-white rounded-md">Filter</button>
                <a href="{{ route('alamat-rt.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a>
            </form>
            @if($rtWithoutAlamat->count())
            <div class="mt-3 p-2 bg-amber-50 border border-amber-200 rounded text-xs text-amber-800 flex gap-2 items-center">
                <i class="bi bi-exclamation-triangle"></i>
                <span>{{ $rtWithoutAlamat->count() }} RT belum memiliki alamat: {{ $rtWithoutAlamat->pluck('kode_rt')->join(', ') }}</span>
                <a href="{{ route('alamat-rt.create') }}" class="ml-auto px-2 py-1 bg-amber-600 text-white rounded text-xs">Konfigurasi</a>
            </div>
            @endif
        </div>
        <x-table :columns="['No','RT','RW','Alamat','Kelurahan','Kecamatan','Kota','Provinsi','Kode Pos','Aksi']" :pagination="$alamats" accent="teal">
            @foreach($alamats as $a)
            <tr class="hover:bg-teal-50/40">
                <td class="px-3 py-2 text-sm">{{ ($alamats->currentPage()-1)*$alamats->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2"><span class="px-2 py-0.5 text-xs bg-teal-100 text-teal-800 rounded-full">{{ $a->rt->kode_rt }}</span></td>
                <td class="px-3 py-2 text-sm">{{ $a->rw ?? '-' }}</td>
                <td class="px-3 py-2 text-sm max-w-[180px] truncate">{{ $a->alamat ?? '-' }}</td>
                <td class="px-3 py-2 text-sm">{{ $a->kelurahan ?? '-' }}</td>
                <td class="px-3 py-2 text-sm">{{ $a->kecamatan ?? '-' }}</td>
                <td class="px-3 py-2 text-sm">{{ $a->kota ?? '-' }}</td>
                <td class="px-3 py-2 text-sm">{{ $a->provinsi ?? '-' }}</td>
                <td class="px-3 py-2 text-sm font-mono">{{ $a->kode_pos ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <x-actions :count="2">
                        @if(auth()->user()->hasPermission('manage_alamat_rt'))
                        <x-actions-item href="{{ route('alamat-rt.edit', $a) }}" icon="bi-pencil" label="Edit" />
                        <x-actions-form action="{{ route('alamat-rt.destroy', $a) }}" method="DELETE" icon="bi-trash" label="Hapus" confirm="Hapus alamat RT?" />
                        @endif
                    </x-actions>
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>
</div>
@endsection
