@extends('layouts.app')
@section('title', 'Detail Inventaris')
@section('content')
<div class="w-full mx-auto max-w-5xl">
    <nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-3">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700">Beranda</a>
        <span class="text-slate-400">/</span>
        <a href="{{ route('inventaris.index') }}" class="hover:text-slate-700">Inventaris</a>
        <span class="text-slate-400">/</span>
        <span class="font-mono text-slate-700">{{ $inventaris->kode_barang }}</span>
    </nav>

    <div class="bg-white border border-slate-200 rounded">
        {{-- Header --}}
        <div class="px-4 sm:px-5 py-4">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex gap-4 min-w-0">
                    @if($inventaris->foto_url)
                        <img src="{{ $inventaris->foto_url }}" class="w-20 h-20 rounded border object-cover shrink-0">
                    @else
                        <div class="w-20 h-20 rounded bg-amber-50 border border-amber-100 flex items-center justify-center shrink-0"><i class="bi bi-box-seam text-amber-600 text-xl"></i></div>
                    @endif
                    <div class="min-w-0">
                        <div class="text-xs text-slate-500">Inventaris</div>
                        <h1 class="text-base font-semibold text-slate-900">{{ $inventaris->nama_barang }} <span class="font-mono text-sm font-normal text-slate-500">({{ $inventaris->kode_barang }})</span></h1>
                        <div class="mt-1.5 flex flex-wrap items-center gap-2">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium border {{ $inventaris->is_tersedia ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }}">{{ $inventaris->is_tersedia ? 'Tersedia '.$inventaris->stok_tersedia : 'Habis' }} / {{ $inventaris->jumlah }} {{ $inventaris->satuan }}</span>
                            @if($inventaris->kondisi=='BAIK')<span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">Baik</span>@elseif($inventaris->kondisi=='RUSAK_RINGAN')<span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded-full">Rusak Ringan</span>@elseif($inventaris->kondisi=='RUSAK_BERAT')<span class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">Rusak Berat</span>@else<span class="px-2 py-0.5 text-xs bg-gray-200 rounded-full">Hilang</span>@endif
                            <span class="text-xs text-slate-500">{{ $inventaris->kategori ?? '-' }} • {{ $inventaris->lokasi ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('inventaris.index') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded text-xs font-medium text-slate-600 hover:bg-slate-50">Kembali</a>
                    <a href="{{ route('inventaris.edit', $inventaris) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold">Edit</a>
                </div>
            </div>
        </div>

        <div class="h-px bg-slate-200"></div>

        @if(!empty($inventaris->fotoUrls))
        <div class="px-4 sm:px-5 py-3" x-data="{ lightbox: null }">
            <div class="text-xs font-semibold text-slate-900 mb-2">Foto Lampiran ({{ count($inventaris->fotoUrls) }}) — klik untuk perbesar</div>
            <div class="flex flex-wrap gap-2">
                @foreach($inventaris->fotoUrls as $url)
                    <img src="{{ $url }}" alt="Foto {{ $inventaris->nama_barang }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded border object-cover cursor-pointer hover:opacity-80 hover:border-amber-300 transition" @click="lightbox='{{ $url }}'">
                @endforeach
            </div>
            <template x-teleport="body">
                <div x-show="lightbox" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70" @click="lightbox=null">
                    <img :src="lightbox" class="max-w-[90vw] max-h-[90vh] rounded-lg shadow-xl object-contain" @click.stop>
                    <button class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/90 flex items-center justify-center" @click="lightbox=null"><i class="bi bi-x-lg"></i></button>
                </div>
            </template>
        </div>
        <div class="h-px bg-slate-200"></div>
        @endif

        <div class="px-4 sm:px-5 py-4">
            <h2 class="text-xs font-semibold text-slate-900 mb-3">Informasi Barang</h2>
            <div class="h-px bg-slate-200 mb-4"></div>
            <div class="grid grid-cols-12 gap-x-6 gap-y-4">
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Kode Barang</div>
                    <div class="mt-1.5 font-mono text-sm font-medium text-slate-900 leading-none">{{ $inventaris->kode_barang }}</div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Kategori</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none">{{ $inventaris->kategori ?? '-' }}</div>
                </div>
                <div class="col-span-6 lg:col-span-2">
                    <div class="text-xs text-slate-500 leading-none">Jumlah / Stok</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none">{{ $inventaris->jumlah }} {{ $inventaris->satuan }} <span class="text-xs font-normal text-slate-500">({{ $inventaris->stok_tersedia }} tersedia)</span></div>
                </div>
                <div class="col-span-6 lg:col-span-2">
                    <div class="text-xs text-slate-500 leading-none">Kondisi</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none">{{ $inventaris->kondisi }}</div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Lokasi</div>
                    <div class="mt-1.5 text-sm text-slate-900 leading-none">{{ $inventaris->lokasi ?? '-' }}</div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Harga Satuan</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none">Rp {{ number_format($inventaris->harga_satuan ?? 0,0,',','.') }}</div>
                </div>
                <div class="col-span-12 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Total Harga</div>
                    <div class="mt-1.5 text-sm font-semibold text-slate-900 leading-none">Rp {{ number_format($inventaris->total_harga ?? 0,0,',','.') }}</div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Tanggal Pengadaan</div>
                    <div class="mt-1.5 text-sm text-slate-900 leading-none">{{ $inventaris->tanggal_pengadaan?->format('d/m/Y') ?? '-' }}</div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Sumber Dana</div>
                    <div class="mt-1.5 text-sm text-slate-900 leading-none">{{ $inventaris->sumber_dana ?? '-' }}</div>
                </div>
                <div class="col-span-12 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Keterangan</div>
                    <div class="mt-1.5 text-sm text-slate-700 leading-none">{{ $inventaris->keterangan ?? '-' }}</div>
                </div>
            </div>
        </div>

        @if($inventaris->peminjaman->count())
        <div class="h-px bg-slate-200"></div>
        <div class="px-4 sm:px-5 py-4">
            <div class="flex items-baseline gap-2 mb-3">
                <h2 class="text-xs font-semibold text-slate-900">Riwayat Peminjaman</h2>
                <span class="text-xs text-slate-500">{{ $inventaris->peminjaman->count() }} data</span>
            </div>
            <div class="border border-slate-200 rounded overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs">
                                <th class="py-2 px-3 font-medium">No</th>
                                <th class="py-2 px-3 font-medium">Tgl Pinjam</th>
                                <th class="py-2 px-3 font-medium">Peminjam</th>
                                <th class="py-2 px-3 font-medium text-center">Jml</th>
                                <th class="py-2 px-3 font-medium">Status</th>
                                <th class="py-2 px-3 font-medium">Rencana Kembali</th>
                                <th class="py-2 px-3 font-medium">Foto Kembali</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @foreach($inventaris->peminjaman as $idx=>$p)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-2.5 px-3 text-center text-slate-500">{{ $idx+1 }}</td>
                                    <td class="py-2.5 px-3">{{ $p->tanggal_pinjam->format('d/m/Y') }}</td>
                                    <td class="py-2.5 px-3 font-medium text-slate-900">{{ $p->nama_peminjam_display }}</td>
                                    <td class="py-2.5 px-3 text-center">{{ $p->jumlah_pinjam }}</td>
                                    <td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded-full text-xs {{ $p->status=='DIPINJAM'?'bg-yellow-100 text-yellow-800':'bg-green-100 text-green-800' }}">{{ $p->status }}</span></td>
                                    <td class="py-2.5 px-3">{{ $p->tanggal_kembali_rencana?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="py-2.5 px-3">
                                        @if($p->fotoKembaliUrls)
                                            <div class="flex flex-wrap gap-1 max-w-[140px]">
                                                @foreach($p->fotoKembaliUrls as $url)
                                                    <img src="{{ $url }}" class="w-8 h-8 rounded border object-cover cursor-pointer hover:opacity-80" onclick="window.open('{{ $url }}','_blank')" title="Klik perbesar">
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
