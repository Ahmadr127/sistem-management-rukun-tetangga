@extends('layouts.app')
@section('title', 'Laporan Inventaris')
@section('content')
<div class="space-y-4">
    <x-card>
        <x-slot name="title">Laporan Inventaris</x-slot>
        <x-slot name="subtitle">Rekap barang, nilai, dan kondisi</x-slot>
        <x-slot name="actions"><a href="javascript:window.print()" class="px-3 py-1.5 border rounded-md text-sm"><i class="bi bi-printer"></i> Cetak</a></x-slot>
    </x-card>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Total Jenis Barang</div><div class="text-2xl font-bold">{{ $laporan['totalBarang'] }}</div></div>
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Total Unit</div><div class="text-2xl font-bold">{{ $laporan['totalUnit'] }}</div></div>
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Total Nilai</div><div class="text-xl font-bold">Rp {{ number_format($laporan['totalNilai'],0,',','.') }}</div></div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <x-card title="Per Kategori"><div class="space-y-1">@forelse($laporan['byKategori'] as $r)<div class="flex justify-between text-sm border-b py-1"><span>{{ $r->kategori ?? '-' }}</span><span>{{ $r->jml_barang }} jenis / {{ $r->total_unit }} unit<br><span class="text-xs text-gray-500">Rp {{ number_format($r->total_nilai,0,',','.') }}</span></span></div>@empty<p class="text-sm text-gray-500">Tidak ada data</p>@endforelse</div></x-card>
        <x-card title="Per Kondisi"><div class="space-y-1">@forelse($laporan['byKondisi'] as $r)<div class="flex justify-between text-sm border-b py-1"><span>{{ $r->kondisi }}</span><span class="font-semibold">{{ $r->jml }}</span></div>@empty<p class="text-sm text-gray-500">-</p>@endforelse</div></x-card>
        <x-card title="Per Lokasi"><div class="space-y-1">@forelse($laporan['byLokasi'] as $r)<div class="flex justify-between text-sm border-b py-1"><span>{{ $r->lokasi ?? '-' }}</span><span class="font-semibold">{{ $r->jml }}</span></div>@empty<p class="text-sm text-gray-500">-</p>@endforelse</div></x-card>
    </div>
    <x-card title="Detail Barang" padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr><th class="px-3 py-2 text-left">Kode</th><th class="px-3 py-2 text-left">Nama</th><th class="px-3 py-2 text-left">Kategori</th><th class="px-3 py-2 text-center">Jml</th><th class="px-3 py-2 text-left">Kondisi</th><th class="px-3 py-2 text-left">Lokasi</th><th class="px-3 py-2 text-right">Nilai</th></tr></thead>
                <tbody class="divide-y">@foreach($laporan['all'] as $inv)<tr><td class="px-3 py-2 font-mono">{{ $inv->kode_barang }}</td><td class="px-3 py-2">{{ $inv->nama_barang }}</td><td class="px-3 py-2">{{ $inv->kategori ?? '-' }}</td><td class="px-3 py-2 text-center">{{ $inv->jumlah }}</td><td class="px-3 py-2">{{ $inv->kondisi }}</td><td class="px-3 py-2">{{ $inv->lokasi ?? '-' }}</td><td class="px-3 py-2 text-right">Rp {{ number_format($inv->total_harga ?? 0,0,',','.') }}</td></tr>@endforeach</tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
