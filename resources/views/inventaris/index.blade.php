@extends('layouts.app')
@section('title', 'Inventaris')
@section('content')
<div class="w-full mx-auto">
    <x-card padding="false" accent="amber">
        <x-slot name="title">Data Inventaris</x-slot>
        <x-slot name="subtitle">Data barang milik RT/RW</x-slot>
        <x-slot name="actions"><a href="{{ route('inventaris.create') }}" class="px-3 py-1.5 bg-sp-primary text-white rounded-md text-sm font-semibold"><i class="bi bi-plus-lg"></i> Tambah Barang</a></x-slot>
        <div class="px-4 py-3 border-b bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]"><label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label><input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, kode, kategori..." class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="w-36"><label class="block text-xs font-semibold text-gray-600 mb-1">Kategori</label><select name="kategori" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"><option value="">Semua</option>@foreach($kategoriList as $kat)<option value="{{ $kat }}" {{ request('kategori')==$kat?'selected':'' }}>{{ $kat }}</option>@endforeach</select></div>
                <div class="w-36"><label class="block text-xs font-semibold text-gray-600 mb-1">Kondisi</label><select name="kondisi" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"><option value="">Semua</option><option value="BAIK" {{ request('kondisi')=='BAIK'?'selected':'' }}>Baik</option><option value="RUSAK_RINGAN" {{ request('kondisi')=='RUSAK_RINGAN'?'selected':'' }}>Rusak Ringan</option><option value="RUSAK_BERAT" {{ request('kondisi')=='RUSAK_BERAT'?'selected':'' }}>Rusak Berat</option><option value="HILANG" {{ request('kondisi')=='HILANG'?'selected':'' }}>Hilang</option></select></div>
                <div class="flex gap-2"><button type="submit" class="px-4 py-1.5 text-sm bg-sp-primary text-white rounded-md">Filter</button><a href="{{ route('inventaris.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a></div>
            </form>
        </div>
        <x-table :columns="['No','Kode','Nama Barang','Kategori','Jumlah','Kondisi','Lokasi','Nilai','Aksi']" :pagination="$inventaris" accent="amber">
            @foreach($inventaris as $inv)
            <tr class="hover:bg-amber-50/40">
                <td class="px-3 py-2 text-sm">{{ ($inventaris->currentPage()-1)*$inventaris->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2 text-sm font-mono">{{ $inv->kode_barang }}</td>
                <td class="px-3 py-2"><div class="text-sm font-medium">{{ $inv->nama_barang }}</div><div class="text-xs text-gray-500">{{ $inv->satuan }}</div></td>
                <td class="px-3 py-2 text-sm">{{ $inv->kategori ?? '-' }}</td>
                <td class="px-3 py-2 text-sm text-center">{{ $inv->jumlah }} <span class="text-xs text-gray-500">({{ $inv->stok_tersedia }} tersedia)</span></td>
                <td class="px-3 py-2">
                    @if($inv->kondisi=='BAIK')<span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">Baik</span>
                    @elseif($inv->kondisi=='RUSAK_RINGAN')<span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded-full">Rusak Ringan</span>
                    @elseif($inv->kondisi=='RUSAK_BERAT')<span class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">Rusak Berat</span>
                    @else<span class="px-2 py-0.5 text-xs bg-gray-200 rounded-full">Hilang</span>@endif
                </td>
                <td class="px-3 py-2 text-sm">{{ $inv->lokasi ?? '-' }}</td>
                <td class="px-3 py-2 text-sm font-mono">Rp {{ number_format($inv->total_harga ?? 0,0,',','.') }}</td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <x-actions>
                        <x-actions-item href="{{ route('inventaris.show', $inv) }}" icon="bi-eye" label="Lihat" />
                        <x-actions-item href="{{ route('inventaris.edit', $inv) }}" icon="bi-pencil" label="Edit" />
                        <x-actions-form action="{{ route('inventaris.destroy', $inv) }}" method="DELETE" icon="bi-trash" label="Hapus" confirm="Hapus barang ini?" />
                    </x-actions>
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>
</div>
@endsection
