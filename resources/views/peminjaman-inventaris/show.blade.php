@extends('layouts.app')
@section('title', 'Detail Peminjaman')
@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <x-card>
        <x-slot name="title">Peminjaman: {{ $peminjaman->inventaris->nama_barang ?? '-' }}</x-slot>
        <x-slot name="actions"><a href="{{ route('peminjaman-inventaris.index') }}" class="px-3 py-1.5 border rounded-md text-sm">Kembali</a> <a href="{{ route('peminjaman-inventaris.edit', $peminjaman) }}" class="px-3 py-1.5 bg-sp-primary text-white rounded-md text-sm">Edit</a></x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <div><span class="text-gray-500">Barang:</span> {{ $peminjaman->inventaris->nama_barang ?? '-' }} ({{ $peminjaman->inventaris->kode_barang ?? '-' }})</div>
            <div><span class="text-gray-500">Jumlah:</span> {{ $peminjaman->jumlah_pinjam }}</div>
            <div><span class="text-gray-500">Peminjam:</span> {{ $peminjaman->nama_peminjam_display }}</div>
            <div><span class="text-gray-500">No HP:</span> {{ $peminjaman->no_hp_peminjam ?? $peminjaman->warga->no_hp ?? '-' }}</div>
            <div><span class="text-gray-500">Tgl Pinjam:</span> {{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</div>
            <div><span class="text-gray-500">Rencana Kembali:</span> {{ $peminjaman->tanggal_kembali_rencana?->format('d/m/Y') ?? '-' }}</div>
            <div><span class="text-gray-500">Aktual Kembali:</span> {{ $peminjaman->tanggal_kembali_aktual?->format('d/m/Y') ?? '-' }}</div>
            <div><span class="text-gray-500">Status:</span> <span class="px-2 py-0.5 rounded-full text-xs {{ $peminjaman->status=='DIPINJAM'?'bg-yellow-100 text-yellow-800':'bg-green-100 text-green-800' }}">{{ $peminjaman->status }}</span> @if($peminjaman->is_terlambat)<span class="text-red-600 text-xs">Terlambat {{ $peminjaman->hari_terlambat }} hari</span>@endif</div>
            <div class="md:col-span-2"><span class="text-gray-500">Keperluan:</span> {{ $peminjaman->keperluan ?? '-' }}</div>
            <div class="md:col-span-2"><span class="text-gray-500">Keterangan:</span> {{ $peminjaman->keterangan ?? '-' }}</div>
            <div class="md:col-span-2"><span class="text-gray-500">Kondisi Kembali:</span> {{ $peminjaman->kondisi_kembali ?? '-' }}</div>
            <div><span class="text-gray-500">Disetujui Oleh:</span> {{ $peminjaman->approver->name ?? '-' }}</div>
        </div>
        @if($peminjaman->status=='DIPINJAM')
        <div class="mt-4 pt-4 border-t">
            <form action="{{ route('peminjaman-inventaris.kembalikan', $peminjaman) }}" method="POST" class="flex flex-wrap gap-3 items-end">
                @csrf @method('PATCH')
                <div><label class="block text-xs font-semibold mb-1">Tgl Kembali</label><input type="date" name="tanggal_kembali_aktual" value="{{ date('Y-m-d') }}" class="px-3 py-1.5 text-sm border rounded-md"></div>
                <div><label class="block text-xs font-semibold mb-1">Status</label><select name="status" class="px-3 py-1.5 text-sm border rounded-md"><option value="DIKEMBALIKAN">Dikembalikan</option><option value="HILANG">Hilang</option><option value="RUSAK">Rusak</option></select></div>
                <div><label class="block text-xs font-semibold mb-1">Kondisi</label><input type="text" name="kondisi_kembali" placeholder="Baik / Rusak" class="px-3 py-1.5 text-sm border rounded-md"></div>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm">Proses Pengembalian</button>
            </form>
        </div>
        @endif
    </x-card>
</div>
@endsection
