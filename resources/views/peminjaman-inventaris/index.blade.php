@extends('layouts.app')
@section('title', 'Peminjaman Inventaris')
@section('content')
<div class="w-full mx-auto">
    <x-card padding="false">
        <x-slot name="title">Peminjaman Inventaris</x-slot>
        <x-slot name="subtitle">Peminjaman & pengembalian barang</x-slot>
        <x-slot name="actions"><a href="{{ route('peminjaman-inventaris.create') }}" class="px-3 py-1.5 bg-sp-primary text-white rounded-md text-sm font-semibold"><i class="bi bi-plus-lg"></i> Pinjam Barang</a></x-slot>
        <div class="px-4 py-3 border-b bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]"><label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label><input type="text" name="search" value="{{ request('search') }}" placeholder="Peminjam, barang, keperluan..." class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="w-36"><label class="block text-xs font-semibold text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"><option value="">Semua</option><option value="DIPINJAM" {{ request('status')=='DIPINJAM'?'selected':'' }}>Dipinjam</option><option value="DIKEMBALIKAN" {{ request('status')=='DIKEMBALIKAN'?'selected':'' }}>Dikembalikan</option><option value="TERLAMBAT" {{ request('status')=='TERLAMBAT'?'selected':'' }}>Terlambat</option><option value="HILANG" {{ request('status')=='HILANG'?'selected':'' }}>Hilang</option><option value="RUSAK" {{ request('status')=='RUSAK'?'selected':'' }}>Rusak</option></select></div>
                <div class="flex gap-2"><button type="submit" class="px-4 py-1.5 text-sm bg-sp-primary text-white rounded-md">Filter</button><a href="{{ route('peminjaman-inventaris.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a></div>
            </form>
        </div>
        <x-table :columns="['Tanggal Pinjam','Barang','Peminjam','Jml','Rencana Kembali','Status','Aksi']" :pagination="$peminjaman">
            @foreach($peminjaman as $p)
            <tr class="hover:bg-gray-50 {{ $p->is_terlambat ? 'bg-red-50' : '' }}">
                <td class="px-3 py-2 text-sm">{{ $p->tanggal_pinjam->format('d/m/Y') }}</td>
                <td class="px-3 py-2"><div class="text-sm font-medium">{{ $p->inventaris->nama_barang ?? '-' }}</div><div class="text-xs text-gray-500">{{ $p->inventaris->kode_barang ?? '-' }}</div></td>
                <td class="px-3 py-2"><div class="text-sm">{{ $p->nama_peminjam_display }}</div><div class="text-xs text-gray-500">{{ $p->warga->nik ?? $p->no_hp_peminjam ?? '' }}</div></td>
                <td class="px-3 py-2 text-center text-sm">{{ $p->jumlah_pinjam }}</td>
                <td class="px-3 py-2 text-sm">{{ $p->tanggal_kembali_rencana?->format('d/m/Y') ?? '-' }} @if($p->is_terlambat)<span class="text-xs text-red-600">({{ $p->hari_terlambat }} hari terlambat)</span>@endif</td>
                <td class="px-3 py-2">
                    @if($p->status=='DIPINJAM')<span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded-full">Dipinjam</span>
                    @elseif($p->status=='DIKEMBALIKAN')<span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">Kembali</span>
                    @elseif($p->status=='TERLAMBAT')<span class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">Terlambat</span>
                    @else<span class="px-2 py-0.5 text-xs bg-gray-200 rounded-full">{{ $p->status }}</span>@endif
                </td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <x-actions>
                        <x-actions-item href="{{ route('peminjaman-inventaris.show', $p) }}" icon="bi-eye" label="Lihat" />
                        @if($p->status=='DIPINJAM')
                        <a href="#" onclick="event.preventDefault(); if(confirm('Kembalikan barang ini?')) document.getElementById('kembali-{{ $p->id }}').submit();" class="flex items-center gap-2 px-3 py-2 text-sm hover:bg-green-50 text-green-600"><i class="bi bi-box-arrow-in-left"></i> Kembalikan</a>
                        <form id="kembali-{{ $p->id }}" action="{{ route('peminjaman-inventaris.kembalikan', $p) }}" method="POST" class="hidden">@csrf @method('PATCH')
                            <input type="hidden" name="tanggal_kembali_aktual" value="{{ date('Y-m-d') }}">
                            <input type="hidden" name="status" value="DIKEMBALIKAN">
                        </form>
                        @endif
                        <x-actions-item href="{{ route('peminjaman-inventaris.edit', $p) }}" icon="bi-pencil" label="Edit" />
                        <x-actions-form action="{{ route('peminjaman-inventaris.destroy', $p) }}" method="DELETE" icon="bi-trash" label="Hapus" confirm="Hapus peminjaman ini?" />
                    </x-actions>
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>
</div>
@endsection
