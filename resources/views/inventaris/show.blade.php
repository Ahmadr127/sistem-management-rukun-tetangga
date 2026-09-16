@extends('layouts.app')
@section('title', 'Detail Inventaris')
@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <x-card>
        <x-slot name="title">{{ $inventaris->nama_barang }} ({{ $inventaris->kode_barang }})</x-slot>
        <x-slot name="actions"><a href="{{ route('inventaris.index') }}" class="px-3 py-1.5 border rounded-md text-sm">Kembali</a> <a href="{{ route('inventaris.edit', $inventaris) }}" class="px-3 py-1.5 bg-sp-primary text-white rounded-md text-sm">Edit</a></x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <div><span class="text-gray-500">Kode:</span> <span class="font-mono">{{ $inventaris->kode_barang }}</span></div>
            <div><span class="text-gray-500">Kategori:</span> {{ $inventaris->kategori ?? '-' }}</div>
            <div><span class="text-gray-500">Jumlah:</span> {{ $inventaris->jumlah }} {{ $inventaris->satuan }} (Tersedia: {{ $inventaris->stok_tersedia }})</div>
            <div><span class="text-gray-500">Kondisi:</span> {{ $inventaris->kondisi }}</div>
            <div><span class="text-gray-500">Lokasi:</span> {{ $inventaris->lokasi ?? '-' }}</div>
            <div><span class="text-gray-500">Harga Satuan:</span> Rp {{ number_format($inventaris->harga_satuan ?? 0,0,',','.') }}</div>
            <div><span class="text-gray-500">Total Harga:</span> Rp {{ number_format($inventaris->total_harga ?? 0,0,',','.') }}</div>
            <div><span class="text-gray-500">Tanggal Pengadaan:</span> {{ $inventaris->tanggal_pengadaan?->format('d/m/Y') ?? '-' }}</div>
            <div><span class="text-gray-500">Sumber Dana:</span> {{ $inventaris->sumber_dana ?? '-' }}</div>
            <div class="md:col-span-2"><span class="text-gray-500">Keterangan:</span> {{ $inventaris->keterangan ?? '-' }}</div>
        </div>
    </x-card>
    @if($inventaris->peminjaman->count())
    <x-card title="Riwayat Peminjaman" padding="false">
        <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50"><tr><th class="px-3 py-2 text-left">Tgl Pinjam</th><th class="px-3 py-2 text-left">Peminjam</th><th class="px-3 py-2 text-center">Jml</th><th class="px-3 py-2 text-left">Status</th><th class="px-3 py-2 text-left">Rencana Kembali</th></tr></thead><tbody class="divide-y">@foreach($inventaris->peminjaman as $p)<tr><td class="px-3 py-2">{{ $p->tanggal_pinjam->format('d/m/Y') }}</td><td class="px-3 py-2">{{ $p->nama_peminjam_display }}</td><td class="px-3 py-2 text-center">{{ $p->jumlah_pinjam }}</td><td class="px-3 py-2">{{ $p->status }}</td><td class="px-3 py-2">{{ $p->tanggal_kembali_rencana?->format('d/m/Y') ?? '-' }}</td></tr>@endforeach</tbody></table></div>
    </x-card>
    @endif
</div>
@endsection
