@extends('layouts.app')
@section('title', 'Kartu Keluarga')
@section('content')
<div class="w-full mx-auto">
    <x-card padding="false">
        <x-slot name="title">Data Kartu Keluarga</x-slot>
        <x-slot name="subtitle">KK & anggota keluarga — pengelompokan RT/RW</x-slot>
        <x-slot name="actions"><a href="{{ route('kartu-keluarga.create') }}" class="px-3 py-1.5 bg-sp-primary text-white rounded-md text-sm font-semibold"><i class="bi bi-plus-lg"></i> Tambah KK</a></x-slot>
        <div class="px-4 py-3 border-b bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]"><label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label><input type="text" name="search" value="{{ request('search') }}" placeholder="No KK, kepala, alamat..." class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="w-28"><label class="block text-xs font-semibold text-gray-600 mb-1">RT</label><input type="text" name="rt" value="{{ request('rt') }}" placeholder="001" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="w-28"><label class="block text-xs font-semibold text-gray-600 mb-1">RW</label><input type="text" name="rw" value="{{ request('rw') }}" placeholder="002" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="flex gap-2"><button type="submit" class="px-4 py-1.5 text-sm bg-sp-primary text-white rounded-md">Filter</button><a href="{{ route('kartu-keluarga.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a></div>
            </form>
        </div>
        <x-table :columns="['No KK','Kepala','Alamat','RT/RW','Jml Anggota','Aksi']" :pagination="$kk">
            @foreach($kk as $k)
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2 font-mono text-sm">{{ $k->no_kk }}</td>
                <td class="px-3 py-2 text-sm font-medium">{{ $k->kepala_keluarga ?? '-' }}</td>
                <td class="px-3 py-2 text-sm max-w-xs truncate">{{ $k->alamat ?? '-' }}</td>
                <td class="px-3 py-2 text-sm text-center">{{ $k->rt ?? '-' }}/{{ $k->rw ?? '-' }}</td>
                <td class="px-3 py-2 text-sm text-center"><span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-xs">{{ $k->warga_count }}</span></td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <x-actions>
                        <x-actions-item href="{{ route('kartu-keluarga.show', $k) }}" icon="bi-eye" label="Lihat" />
                        <x-actions-item href="{{ route('kartu-keluarga.edit', $k) }}" icon="bi-pencil" label="Edit" />
                        <x-actions-form action="{{ route('kartu-keluarga.destroy', $k) }}" method="DELETE" icon="bi-trash" label="Hapus" confirm="Hapus KK ini?" />
                    </x-actions>
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>
</div>
@endsection
