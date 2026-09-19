@extends('layouts.app')
@section('title', 'Detail Peminjaman')
@section('content')
@php
    $statusMap = [
        'DIPINJAM' => 'bg-amber-50 text-amber-800 border-amber-200',
        'DIKEMBALIKAN' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'TERLAMBAT' => 'bg-red-50 text-red-700 border-red-200',
        'HILANG' => 'bg-slate-800 text-white border-slate-800',
        'RUSAK' => 'bg-orange-50 text-orange-800 border-orange-200',
    ];
@endphp
<div class="w-full mx-auto max-w-5xl">
    <nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-3">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700">Beranda</a>
        <span class="text-slate-400">/</span>
        <a href="{{ route('peminjaman-inventaris.index') }}" class="hover:text-slate-700">Peminjaman</a>
        <span class="text-slate-400">/</span>
        <span class="font-mono text-slate-700">#{{ $peminjaman->id }} • {{ $peminjaman->inventaris->kode_barang ?? '-' }}</span>
    </nav>

    <div class="bg-white border border-slate-200 rounded">
        {{-- Header --}}
        <div class="px-4 sm:px-5 py-4">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex gap-3 min-w-0">
                    <div class="flex gap-1.5 shrink-0" x-data="{ lightbox:null }">
                        @if($peminjaman->inventaris?->fotoUrls)
                            <div class="flex gap-1.5 flex-wrap max-w-[160px]">
                                @foreach($peminjaman->inventaris->fotoUrls as $url)
                                    <img src="{{ $url }}" class="w-14 h-14 rounded border object-cover cursor-pointer hover:opacity-80" @click="lightbox='{{ $url }}'">
                                @endforeach
                            </div>
                        @else
                            <div class="w-14 h-14 rounded bg-amber-50 border border-amber-100 flex items-center justify-center shrink-0"><i class="bi bi-box-seam text-amber-600"></i></div>
                        @endif
                        <template x-teleport="body">
                            <div x-show="lightbox" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70" @click="lightbox=null">
                                <img :src="lightbox" class="max-w-[90vw] max-h-[90vh] rounded-lg shadow-xl object-contain">
                            </div>
                        </template>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs text-slate-500">Peminjaman</div>
                        <h1 class="text-base font-semibold text-slate-900 leading-tight">{{ $peminjaman->inventaris->nama_barang ?? '-' }} <span class="font-mono text-sm font-normal text-slate-500">({{ $peminjaman->inventaris->kode_barang ?? '-' }})</span></h1>
                        <div class="mt-1.5 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $statusMap[$peminjaman->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">{{ $peminjaman->status }}</span>
                            @if($peminjaman->is_terlambat)<span class="inline-flex items-center gap-1 text-xs font-medium text-red-700"><i class="bi bi-exclamation-triangle"></i> Terlambat {{ $peminjaman->hari_terlambat }} hari</span>@endif
                            <span class="text-xs text-slate-500">{{ $peminjaman->jumlah_pinjam }} unit • {{ $peminjaman->inventaris->lokasi ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('peminjaman-inventaris.index') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded text-xs font-medium text-slate-600 hover:bg-slate-50">Kembali</a>
                    <a href="{{ route('peminjaman-inventaris.edit', $peminjaman) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded bg-sp-primary hover:bg-sp-primary-dark text-white text-xs font-semibold">Edit</a>
                </div>
            </div>
        </div>

        <div class="h-px bg-slate-200"></div>

        <div class="px-4 sm:px-5 py-4">
            <div class="flex items-baseline justify-between gap-3 mb-3">
                <h2 class="text-xs font-semibold text-slate-900">Informasi Peminjaman</h2>
                <span class="text-xs text-slate-500 hidden sm:block">ID #{{ $peminjaman->id }} • {{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</span>
            </div>
            <div class="h-px bg-slate-200 mb-4"></div>

            <div class="grid grid-cols-12 gap-x-6 gap-y-4">
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Peminjam</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none truncate">{{ $peminjaman->nama_peminjam_display }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ $peminjaman->warga->nik ?? $peminjaman->no_hp_peminjam ?? '' }}</div>
                </div>
                <div class="col-span-6 lg:col-span-2">
                    <div class="text-xs text-slate-500 leading-none">No HP</div>
                    <div class="mt-1.5 text-sm text-slate-900 leading-none">{{ $peminjaman->no_hp_peminjam ?? $peminjaman->warga->no_hp ?? '-' }}</div>
                </div>
                <div class="col-span-6 lg:col-span-2">
                    <div class="text-xs text-slate-500 leading-none">Jumlah</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none">{{ $peminjaman->jumlah_pinjam }} {{ $peminjaman->inventaris->satuan ?? 'unit' }}</div>
                </div>
                <div class="col-span-6 lg:col-span-2">
                    <div class="text-xs text-slate-500 leading-none">Tgl Pinjam</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none">{{ $peminjaman->tanggal_pinjam->format('d/m/Y') }}</div>
                </div>
                <div class="col-span-6 lg:col-span-2">
                    <div class="text-xs text-slate-500 leading-none">Rencana Kembali</div>
                    <div class="mt-1.5 text-sm text-slate-900 leading-none">{{ $peminjaman->tanggal_kembali_rencana?->format('d/m/Y') ?? '-' }}</div>
                </div>
                <div class="col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Aktual Kembali</div>
                    <div class="mt-1.5 text-sm text-slate-900 leading-none">{{ $peminjaman->tanggal_kembali_aktual?->format('d/m/Y') ?? '-' }}</div>
                </div>
                <div class="col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Kondisi Kembali</div>
                    <div class="mt-1.5 text-sm text-slate-900 leading-none">{{ $peminjaman->kondisi_kembali ?? '-' }}</div>
                </div>
                <div class="col-span-12 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Disetujui Oleh</div>
                    <div class="mt-1.5 text-sm text-slate-900 leading-none">{{ $peminjaman->approver->name ?? '-' }}</div>
                </div>
                <div class="col-span-12">
                    <div class="text-xs text-slate-500 leading-none">Keperluan</div>
                    <div class="mt-1.5 text-sm text-slate-700 leading-snug">{{ $peminjaman->keperluan ?? '-' }}</div>
                </div>
                <div class="col-span-12">
                    <div class="text-xs text-slate-500 leading-none">Keterangan</div>
                    <div class="mt-1.5 text-sm text-slate-700 leading-snug">{{ $peminjaman->keterangan ?? '-' }}</div>
                </div>
            </div>

            @if($peminjaman->fotoKembaliUrls)
            <div class="mt-4 pt-4 border-t border-slate-100" x-data="{ lightbox:null }">
                <div class="text-xs text-slate-500 mb-2">Foto Kembali ({{ count($peminjaman->fotoKembaliUrls) }}) — klik untuk perbesar</div>
                <div class="flex flex-wrap gap-2">
                    @foreach($peminjaman->fotoKembaliUrls as $url)
                        <img src="{{ $url }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded border object-cover cursor-pointer hover:opacity-80" @click="lightbox='{{ $url }}'">
                    @endforeach
                </div>
                <template x-teleport="body">
                    <div x-show="lightbox" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70" @click="lightbox=null">
                        <img :src="lightbox" class="max-w-[90vw] max-h-[90vh] rounded-lg shadow-xl object-contain" @click.stop>
                        <button class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/90 flex items-center justify-center" @click="lightbox=null"><i class="bi bi-x-lg"></i></button>
                    </div>
                </template>
            </div>
            @endif
        </div>

        @if($peminjaman->status=='DIPINJAM')
        <div class="h-px bg-slate-200"></div>
        <div class="px-4 sm:px-5 py-4 bg-slate-50/50">
            <h3 class="text-xs font-semibold text-slate-900 mb-3 flex items-center gap-1.5"><i class="bi bi-box-arrow-in-left text-green-600"></i> Proses Pengembalian</h3>
            <form action="{{ route('peminjaman-inventaris.kembalikan', $peminjaman) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-12 gap-3 items-end">
                @csrf @method('PATCH')
                <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Tgl Kembali</label>
                    <input type="date" name="tanggal_kembali_aktual" value="{{ date('Y-m-d') }}" class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded bg-white">
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded bg-white"><option value="DIKEMBALIKAN">Dikembalikan</option><option value="HILANG">Hilang</option><option value="RUSAK">Rusak</option></select>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Kondisi</label>
                    <input type="text" name="kondisi_kembali" placeholder="Baik / Rusak" class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded bg-white">
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-3" x-data="{ previews: [] }">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Foto Kembali</label>
                    <input type="file" name="foto_kembali[]" multiple accept="image/*" class="w-full text-xs file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-green-50 file:text-green-700" @change="previews=[]; for(let f of $event.target.files) previews.push(URL.createObjectURL(f))">
                    <div class="flex flex-wrap gap-1.5 mt-1.5" x-show="previews.length">
                        <template x-for="(src,i) in previews" :key="i"><img :src="src" class="w-12 h-12 rounded border object-cover"></template>
                    </div>
                </div>
                <div class="col-span-12 flex justify-end">
                    <button type="submit" class="px-4 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-semibold">Proses Pengembalian</button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
