@extends('layouts.app')
@section('title', 'Laporan Inventaris')
@section('content')
<div class="w-full mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
        <nav class="flex items-center gap-1.5 text-xs text-slate-500">
            <a href="{{ route('dashboard') }}" class="hover:text-slate-700">Beranda</a>
            <span class="text-slate-400">/</span>
            <a href="{{ route('inventaris.index') }}" class="hover:text-slate-700">Inventaris</a>
            <span class="text-slate-400">/</span>
            <span class="font-semibold text-slate-700">Laporan Peminjaman</span>
        </nav>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 hidden sm:inline">Total pinjam: {{ $peminjaman->total() }} • Inventaris: {{ $laporan['totalBarang'] }} jenis</span>
        </div>
    </div>

    <x-card padding="false" accent="amber">
        <x-slot name="title">Laporan Peminjaman — Tanggal Pinjam & Kembali</x-slot>
        <x-slot name="subtitle">Kolom: tanggal, barang (nama/kode), lokasi, kondisi + detail</x-slot>
        <div class="px-4 py-3 border-b bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama barang, kode, peminjam..." class="w-full px-3 py-1.5 text-sm border rounded-md bg-white">
                </div>
                <div class="w-40">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Barang</label>
                    <select name="inventaris_id" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white">
                        <option value="">Semua</option>
                        @foreach($inventarisList as $inv)
                            <option value="{{ $inv->id }}" {{ request('inventaris_id')==$inv->id?'selected':'' }}>{{ $inv->kode_barang }} - {{ $inv->nama_barang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white">
                        <option value="">Semua</option>
                        <option value="DIPINJAM" {{ request('status')=='DIPINJAM'?'selected':'' }}>Dipinjam</option>
                        <option value="DIKEMBALIKAN" {{ request('status')=='DIKEMBALIKAN'?'selected':'' }}>Kembali</option>
                        <option value="TERLAMBAT" {{ request('status')=='TERLAMBAT'?'selected':'' }}>Terlambat</option>
                        <option value="HILANG" {{ request('status')=='HILANG'?'selected':'' }}>Hilang</option>
                        <option value="RUSAK" {{ request('status')=='RUSAK'?'selected':'' }}>Rusak</option>
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tgl Pinjam</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white">
                </div>
                <div class="w-32">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tgl</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-1.5 text-sm bg-amber-500 hover:bg-amber-600 text-white rounded-md font-medium">Filter</button>
                    <a href="{{ route('inventaris.laporan') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a>
                </div>
            </form>
        </div>

        <x-table :columns="['No','Tgl Pinjam','Tgl Kembali','Nama Barang','Kode','Lokasi','Kondisi Barang','Kondisi Kembali','Peminjam','Status','Detail']" :pagination="$peminjaman" accent="amber">
            @forelse($peminjaman as $p)
                <tr class="hover:bg-amber-50/40 text-xs">
                    <td class="px-3 py-2 text-center text-slate-500">{{ ($peminjaman->currentPage()-1)*$peminjaman->perPage() + $loop->iteration }}</td>
                    <td class="px-3 py-2 font-mono">{{ $p->tanggal_pinjam->format('d/m/Y') }}</td>
                    <td class="px-3 py-2 font-mono">{{ $p->tanggal_kembali_aktual?->format('d/m/Y') ?? $p->tanggal_kembali_rencana?->format('d/m/Y') ?? '-' }} @if($p->is_terlambat)<span class="text-red-600">({{ $p->hari_terlambat }}h)</span>@endif</td>
                    <td class="px-3 py-2">
                        <div class="font-medium text-slate-900 flex items-center gap-1.5">
                            @if($p->inventaris?->fotoUrls)
                                <img src="{{ $p->inventaris->fotoUrls[0] }}" class="w-6 h-6 rounded border object-cover hidden sm:block">
                            @endif
                            {{ $p->inventaris->nama_barang ?? '-' }}
                        </div>
                    </td>
                    <td class="px-3 py-2 font-mono">{{ $p->inventaris->kode_barang ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $p->inventaris->lokasi ?? '-' }}</td>
                    <td class="px-3 py-2">
                        @php $kondisi = $p->inventaris->kondisi ?? '-'; @endphp
                        @if($kondisi=='BAIK')<span class="px-1.5 py-0.5 bg-green-100 text-green-800 rounded-full text-xs">Baik</span>
                        @elseif($kondisi=='RUSAK_RINGAN')<span class="px-1.5 py-0.5 bg-yellow-100 text-yellow-800 rounded-full">Ringan</span>
                        @elseif($kondisi=='RUSAK_BERAT')<span class="px-1.5 py-0.5 bg-red-100 text-red-800 rounded-full">Berat</span>
                        @else<span class="px-1.5 py-0.5 bg-gray-100 rounded-full text-xs">{{ $kondisi }}</span>@endif
                    </td>
                    <td class="px-3 py-2">{{ $p->kondisi_kembali ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $p->nama_peminjam_display }}</td>
                    <td class="px-3 py-2">
                        @if($p->status=='DIPINJAM')<span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-xs">Dipinjam</span>
                        @elseif($p->status=='DIKEMBALIKAN')<span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs">Kembali</span>
                        @elseif($p->status=='TERLAMBAT')<span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-xs">Terlambat</span>
                        @else<span class="px-2 py-0.5 bg-gray-100 rounded-full text-xs">{{ $p->status }}</span>@endif
                    </td>
                    <td class="px-3 py-2 text-center">
                        <a href="{{ route('peminjaman-inventaris.show', $p) }}" class="inline-flex items-center gap-1 px-2 py-1 rounded border border-slate-200 text-xs hover:bg-slate-50"><i class="bi bi-eye"></i> Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="11" class="px-4 py-8 text-center text-gray-500">Tidak ada data peminjaman.</td></tr>
            @endforelse
        </x-table>
    </x-card>
</div>
@endsection
