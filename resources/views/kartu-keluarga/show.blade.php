@extends('layouts.app')
@section('title', 'Detail KK')
@section('content')
@php
    $aktif = $kartuKeluarga->warga->where('status_warga','AKTIF')->count() > 0;
@endphp
<div class="w-full mx-auto max-w-5xl">
    {{-- Breadcrumb compact --}}
    <nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-3">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700">Beranda</a>
        <span class="text-slate-400">/</span>
        <a href="{{ route('kartu-keluarga.index') }}" class="hover:text-slate-700">Kartu Keluarga</a>
        <span class="text-slate-400">/</span>
        <span class="font-mono text-slate-700">{{ $kartuKeluarga->no_kk }}</span>
    </nav>

    {{-- Single container --}}
    <div class="bg-white border border-slate-200 rounded">
        {{-- Page header --}}
        <div class="px-4 sm:px-5 py-4">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="min-w-0">
                    <div class="text-xs text-slate-500">Kartu Keluarga</div>
                    <div class="flex flex-wrap items-center gap-2 mt-0.5">
                        <h1 class="text-base font-semibold text-slate-900 tracking-tight">KK <span class="font-mono font-semibold">{{ $kartuKeluarga->no_kk }}</span></h1>
                        <button onclick="navigator.clipboard.writeText('{{ $kartuKeluarga->no_kk }}'); window.Toast&&Toast.success('No KK disalin')" class="inline-flex items-center justify-center w-5 h-5 rounded text-slate-400 hover:text-slate-700 hover:bg-slate-100" title="Salin"><i class="bi bi-copy text-xs"></i></button>
                        
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $aktif ? 'text-emerald-700' : 'text-slate-500' }}"><span class="w-1.5 h-1.5 rounded-full {{ $aktif ? 'bg-emerald-600' : 'bg-slate-400' }}"></span>{{ $aktif ? 'Aktif' : 'Tidak aktif' }}</span>
                    </div>
                    
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('kartu-keluarga.index') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-700">Kembali</a>
                    <a href="{{ route('kartu-keluarga.edit', $kartuKeluarga) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded bg-sp-primary hover:bg-sp-primary-dark text-white text-xs font-semibold">Edit KK</a>
                </div>
            </div>
        </div>

        <div class="h-px bg-slate-200"></div>

        {{-- Identitas & Wilayah --}}
        <div class="px-4 sm:px-5 py-4">
            <div class="flex items-baseline justify-between gap-3 mb-3">
                <h2 class="text-xs font-semibold text-slate-900">Identitas & Wilayah Domisili</h2>
                <span class="text-xs text-slate-500 hidden sm:block">-</span>
            </div>
            <div class="h-px bg-slate-200 mb-4"></div>

            {{-- Grid sejajar 12 kolom (sejajar vertikal di semua row) --}}
            <div class="grid grid-cols-12 gap-x-6 gap-y-5">
                {{-- Row 1: Nomor (4) | Kepala (4) | RT (2) | RW (2) → 4+4+2+2=12, selaras dengan baris 4+4+4 di bawah pada garis 4 dan 8 --}}
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Nomor Kartu Keluarga</div>
                    <div class="mt-1.5 font-mono text-sm font-medium text-slate-900 flex items-center gap-1.5 leading-none">{{ $kartuKeluarga->no_kk }}<button onclick="navigator.clipboard.writeText('{{ $kartuKeluarga->no_kk }}');window.Toast&&Toast.success('No KK disalin')" class="inline-flex items-center justify-center w-5 h-5 rounded text-slate-400 hover:text-slate-600 hover:bg-slate-100 shrink-0"><i class="bi bi-copy text-xs"></i></button></div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Kepala Keluarga</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none truncate">{{ $kartuKeluarga->kepala_keluarga ?? '-' }}</div>
                </div>
                <div class="col-span-6 lg:col-span-2">
                    <div class="text-xs text-slate-500 leading-none">RT</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none">{{ $kartuKeluarga->rtRelation?->kode_rt ?? $kartuKeluarga->rt ?? '-' }}</div>
                </div>
                <div class="col-span-6 lg:col-span-2">
                    <div class="text-xs text-slate-500 leading-none">RW</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none">{{ $kartuKeluarga->rw ?? '-' }}</div>
                </div>

                {{-- Row 2: 3 kolom sejajar (4+4+4) --}}
                <div class="col-span-12 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Alamat Detail</div>
                    <div class="mt-1.5 text-sm text-slate-900 leading-snug break-words">{{ $kartuKeluarga->alamat ?? '-' }}</div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Dusun / Lingkungan</div>
                    <div class="mt-1.5 text-sm text-slate-700 leading-none">{{ $kartuKeluarga->dusun ?? '-' }}</div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Desa / Kelurahan</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none">{{ $kartuKeluarga->desa ?? '-' }}</div>
                </div>

                {{-- Row 3: 3 kolom sejajar (4+4+4) --}}
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Kecamatan</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none">{{ $kartuKeluarga->kecamatan ?? '-' }}</div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Kabupaten / Kota</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none">{{ $kartuKeluarga->kabupaten ?? '-' }}</div>
                </div>
                <div class="col-span-12 lg:col-span-4">
                    <div class="text-xs text-slate-500 leading-none">Provinsi & Kode Pos</div>
                    <div class="mt-1.5 text-sm font-medium text-slate-900 leading-none">{{ $kartuKeluarga->provinsi ?? '-' }} <span class="font-normal text-slate-500">/</span> {{ $kartuKeluarga->kode_pos ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="h-px bg-slate-200"></div>

        {{-- Anggota Keluarga --}}
        <div class="px-4 sm:px-5 py-4">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="flex items-baseline gap-2">
                    <h2 class="text-xs font-semibold text-slate-900">Anggota Keluarga</h2>
                    <span class="text-xs text-slate-500">{{ $kartuKeluarga->warga->count() }} Anggota</span>
                </div>
                @if(auth()->user()->hasPermission('manage_warga'))
                    @include('warga.partials._modal', [
                        'kartuKeluarga' => $kartuKeluarga,
                        'rts' => $rts,
                        'action' => route('warga.store'),
                        'method' => 'POST',
                        'warga' => null,
                        'modalId' => 'addWargaModal',
                        'title' => 'Tambah Anggota Keluarga',
                        'trigger' => '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-sp-primary hover:bg-sp-primary-dark text-white text-xs font-semibold cursor-pointer">Tambah</span>'
                    ])
                @endif
            </div>

            <div class="border border-slate-200 rounded overflow-hidden">
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs">
                                <th class="py-2 px-3 font-medium text-center w-8">No</th>
                                <th class="py-2 px-3 font-medium">Nama</th>
                                <th class="py-2 px-3 font-medium">NIK</th>
                                <th class="py-2 px-3 font-medium">Hubungan</th>
                                <th class="py-2 px-3 font-medium hidden lg:table-cell">Pekerjaan</th>
                                <th class="py-2 px-3 font-medium text-center">Status</th>
                                <th class="py-2 px-3 font-medium text-right w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($kartuKeluarga->warga as $idx => $w)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-2.5 px-3 text-center text-slate-500">{{ $idx+1 }}</td>
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                                                @if($w->foto_url)<img src="{{ $w->foto_url }}" class="w-full h-full object-cover">@else<span class="text-[10px] font-semibold text-slate-600">{{ $w->inisial }}</span>@endif
                                            </span>
                                            <span class="font-medium text-slate-900">{{ $w->nama }}</span>
                                            <!-- @if($w->nama === $kartuKeluarga->kepala_keluarga)<span class="text-[10px] leading-none px-1 py-0.5 rounded bg-slate-900 text-white">Kepala</span>@endif  jangan ditambah lagi -->
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5 sm:hidden">{{ $w->nik }} • {{ $w->jenis_kelamin }}</div>
                                    </td>
                                    <td class="py-2.5 px-3 font-mono text-xs text-slate-700 hidden sm:table-cell">{{ $w->nik }}</td>
                                    <td class="py-2.5 px-3 text-slate-700">{{ $w->hubungan_keluarga ?? '-' }}</td>
                                    <td class="py-2.5 px-3 text-slate-600 hidden lg:table-cell">{{ $w->pekerjaan ?? '-' }}</td>
                                    <td class="py-2.5 px-3 text-center">@if($w->status_warga=='AKTIF')<span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>@elseif($w->status_warga=='PINDAH')<span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Pindah</span>@else<span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">Meninggal</span>@endif</td>
                                    <td class="py-2.5 px-3 text-right">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('warga.show', $w) }}" class="w-6 h-6 inline-flex items-center justify-center rounded text-slate-500 hover:text-slate-700 hover:bg-slate-100" title="Lihat"><i class="bi bi-eye text-xs"></i></a>
                                            @if(auth()->user()->hasPermission('manage_warga'))
                                                @include('warga.partials._modal', [
                                                    'kartuKeluarga' => $kartuKeluarga,
                                                    'rts' => $rts,
                                                    'action' => route('warga.update', $w),
                                                    'method' => 'PUT',
                                                    'warga' => $w,
                                                    'modalId' => 'editWargaModal'.$w->id,
                                                    'title' => 'Edit Anggota: '.$w->nama,
                                                    'trigger' => '<span class="w-6 h-6 inline-flex items-center justify-center rounded text-slate-500 hover:text-slate-700 hover:bg-slate-100" title="Edit"><i class="bi bi-pencil text-xs"></i></span>'
                                                ])
                                                <form action="{{ route('warga.destroy', $w) }}" method="POST" class="inline" onsubmit="return confirm('Hapus anggota ini?')">@csrf @method('DELETE')<input type="hidden" name="redirect_to" value="{{ route('kartu-keluarga.show', $kartuKeluarga) }}"><button class="w-6 h-6 inline-flex items-center justify-center rounded text-slate-400 hover:text-red-600 hover:bg-red-50" title="Hapus"><i class="bi bi-trash text-xs"></i></button></form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-8 text-center text-slate-500 text-xs">Belum ada anggota keluarga. Gunakan tombol Tambah.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Mobile stacked --}}
                <div class="sm:hidden divide-y divide-slate-100">
                    @forelse($kartuKeluarga->warga as $w)
                        <div class="p-3 flex gap-3">
                            <span class="w-7 h-7 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">@if($w->foto_url)<img src="{{ $w->foto_url }}" class="w-full h-full object-cover">@else<span class="text-xs font-semibold text-slate-600">{{ $w->inisial }}</span>@endif</span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5"><span class="text-sm font-medium text-slate-900">{{ $w->nama }}</span></div>
                                <div class="font-mono text-xs text-slate-600">{{ $w->nik }}</div>
                                <div class="text-xs text-slate-500 mt-1">{{ $w->hubungan_keluarga ?? '-' }} • {{ $w->pekerjaan ?? '-' }}</div>
                            </div>
                            <span class="text-xs font-medium {{ $w->status_warga=='AKTIF'?'text-emerald-700':'text-slate-500' }}">{{ $w->status_warga }}</span>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-500">Belum ada anggota</div>
                    @endforelse
                </div>
            </div>
            <div class="mt-2 text-xs text-slate-500">Menampilkan {{ $kartuKeluarga->warga->count() }} anggota</div>
        </div>
    </div>
</div>
@endsection
