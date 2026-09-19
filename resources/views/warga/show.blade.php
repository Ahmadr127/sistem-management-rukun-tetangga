@extends('layouts.app')
@section('title', 'Detail Warga')
@section('content')
@php
    $fotoUrl = $warga->foto_url;
    $inisial = $warga->inisial;
    $statusColor = $warga->status_warga=='AKTIF' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($warga->status_warga=='PINDAH' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-100 text-slate-600 border-slate-200');
    $kk = $warga->kartuKeluarga;
    $rtKode = $warga->rt?->kode_rt ?? '-';
    $rwKode = $kk?->rw ?? $warga->rt?->alamatRt?->rw ?? '-';
    $waNumber = preg_replace('/[^0-9]/', '', $warga->no_hp ?? '');
    if($waNumber && substr($waNumber,0,1)=='0') $waNumber = '62'.substr($waNumber,1);
@endphp
<div class="w-full mx-auto space-y-4" x-data="{ tab: 'pribadi' }">
    {{-- Breadcrumb + header actions --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <nav class="flex items-center gap-1.5 text-xs text-slate-500">
            <a href="{{ route('dashboard') }}" class="hover:text-teal-700">Beranda</a>
            <i class="bi bi-chevron-right text-[10px]"></i>
            <a href="{{ route('warga.index') }}" class="hover:text-teal-700">Warga</a>
            <i class="bi bi-chevron-right text-[10px]"></i>
            <span class="font-mono font-semibold text-teal-700">{{ $warga->nik }}</span>
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route('warga.index') }}" class="px-3 py-1.5 rounded-md border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 inline-flex items-center gap-1.5"><i class="bi bi-arrow-left"></i> Kembali</a>
            <a href="{{ route('warga.edit', $warga) }}" class="px-3.5 py-1.5 rounded-md bg-[#007774] hover:bg-[#006663] text-white text-xs font-semibold inline-flex items-center gap-1.5 shadow-sm"><i class="bi bi-pencil"></i> Edit</a>
        </div>
    </div>

    {{-- Profile Summary Header (ref: Clean Profile Summary) --}}
    <section class="bg-white border border-slate-200 rounded-lg p-4 sm:p-5">
        <div class="flex items-start gap-4">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                @if($fotoUrl)
                    <img src="{{ $fotoUrl }}" alt="Foto {{ $warga->nama }}" class="w-full h-full object-cover">
                @else
                    <span class="text-slate-600 font-bold text-xl sm:text-2xl">{{ $inisial }}</span>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 truncate">{{ $warga->nama }}</h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border {{ $statusColor }}">{{ $warga->status_warga }}</span>
                    @if($warga->hubungan_keluarga)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">{{ $warga->hubungan_keluarga }}</span>
                    @endif
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-teal-50 text-teal-800 border border-teal-200">RT {{ $rtKode }} / RW {{ $rwKode }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="text-xs text-slate-500">NIK:</span>
                    <span class="font-mono text-xs font-semibold text-slate-800 tracking-wide">{{ $warga->nik }}</span>
                    <button onclick="navigator.clipboard.writeText('{{ $warga->nik }}'); window.Toast && Toast.success('NIK disalin')" class="p-1 rounded text-slate-400 hover:text-slate-700 hover:bg-slate-100" title="Salin NIK"><i class="bi bi-copy text-[14px]"></i></button>
                    @if($warga->umur)<span class="text-xs text-slate-500">• {{ $warga->umur }} th</span>@endif
                    <span class="text-xs text-slate-500">• {{ $warga->jenis_kelamin=='L'?'Laki-laki':'Perempuan' }}</span>
                </div>
                <div class="flex flex-wrap gap-2 mt-3">
                    @if($warga->no_hp)
                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-slate-200 bg-slate-50 text-slate-700 text-xs font-semibold hover:bg-slate-100"><i class="bi bi-whatsapp text-emerald-600"></i> Hubungi WhatsApp</a>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-slate-200 bg-slate-50 text-slate-400 text-xs font-semibold"><i class="bi bi-whatsapp"></i> No HP -</span>
                    @endif
                    @if($kk)
                        <a href="{{ route('kartu-keluarga.show', $kk) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-slate-200 bg-slate-50 text-slate-700 text-xs font-semibold hover:bg-slate-100"><i class="bi bi-folder2-open"></i> Lihat Kartu Keluarga</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Tabs --}}
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <div class="flex border-b border-slate-200">
            <button @click="tab='pribadi'" :class="tab==='pribadi' ? 'border-teal-700 text-teal-700 font-semibold border-b-2' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="flex-1 py-2.5 text-xs text-center transition-colors">Data Pribadi</button>
            <button @click="tab='domisili'" :class="tab==='domisili' ? 'border-teal-700 text-teal-700 font-semibold border-b-2' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="flex-1 py-2.5 text-xs text-center transition-colors">Domisili</button>
            <button @click="tab='keluarga'" :class="tab==='keluarga' ? 'border-teal-700 text-teal-700 font-semibold border-b-2' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="flex-1 py-2.5 text-xs text-center transition-colors">Keluarga</button>
        </div>

        {{-- Pane Pribadi --}}
        <div x-show="tab==='pribadi'" class="divide-y divide-slate-100 text-xs">
            <div class="py-3 px-4 flex justify-between items-center gap-4">
                <span class="text-slate-500">NIK</span>
                <div class="flex items-center gap-1.5"><span class="font-mono font-semibold text-slate-900">{{ $warga->nik }}</span><button onclick="navigator.clipboard.writeText('{{ $warga->nik }}');window.Toast&&Toast.success('NIK disalin')" class="text-slate-400 hover:text-slate-700"><i class="bi bi-copy text-[12px]"></i></button></div>
            </div>
            <div class="py-3 px-4 flex justify-between items-center gap-4">
                <span class="text-slate-500">Tempat, Tgl Lahir</span><span class="font-semibold text-slate-900 text-right">{{ $warga->tempat_lahir ?? '-' }}, {{ $warga->tanggal_lahir?->format('d/m/Y') ?? '-' }} @if($warga->umur) ({{ $warga->umur }} th) @endif</span>
            </div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Jenis Kelamin</span><span class="font-semibold text-slate-900">{{ $warga->jenis_kelamin=='L'?'Laki-laki':'Perempuan' }}</span></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Agama</span><span class="font-semibold text-slate-900">{{ $warga->agama ?? '-' }}</span></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Pendidikan</span><span class="font-semibold text-slate-900">{{ $warga->pendidikan ?? '-' }}</span></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Pekerjaan</span><span class="font-semibold text-slate-900 text-right">{{ $warga->pekerjaan ?? '-' }}</span></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Status Kawin</span><span class="font-semibold text-slate-900">{{ $warga->status_perkawinan ?? '-' }}</span></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Hub. Keluarga</span><span class="font-semibold text-slate-900">{{ $warga->hubungan_keluarga ?? '-' }}</span></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">No KK Terkait</span><div class="flex items-center gap-1.5">@if($kk)<span class="font-mono font-semibold text-slate-900">{{ $kk->no_kk }}</span><span class="text-slate-500">({{ $kk->kepala_keluarga }})</span>@else<span class="text-slate-400">-</span>@endif</div></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">No HP / WA</span><div class="flex items-center gap-2"><span class="font-mono font-semibold text-slate-900">{{ $warga->no_hp ?? '-' }}</span>@if($warga->no_hp)<a href="https://wa.me/{{ $waNumber }}" target="_blank" class="text-teal-700 hover:underline font-semibold">WA</a>@endif</div></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Gol. Darah</span><span class="font-semibold text-slate-900">{{ $warga->golongan_darah ?? '-' }}</span></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Kewarganegaraan</span><span class="font-semibold text-slate-900">{{ $warga->kewarganegaraan ?? '-' }}</span></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Nama Ayah</span><span class="font-semibold text-slate-900">{{ $warga->nama_ayah ?? '-' }}</span></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Nama Ibu</span><span class="font-semibold text-slate-900">{{ $warga->nama_ibu ?? '-' }}</span></div>
        </div>

        {{-- Pane Domisili --}}
        <div x-show="tab==='domisili'" x-cloak class="divide-y divide-slate-100 text-xs">
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Wilayah RT/RW</span><span class="font-semibold text-slate-900">RT {{ $rtKode }} / RW {{ $rwKode }}</span></div>
            <div class="py-3 px-4">
                <div class="text-slate-500 mb-1">Alamat Wilayah RT (Master)</div>
                @if($warga->rt?->alamatRt)
                    <div class="font-medium text-slate-900">{{ $warga->rt->alamatRt->alamat ?? '-' }}</div>
                    <div class="text-[11px] text-slate-500">RW {{ $warga->rt->alamatRt->rw }}, {{ $warga->rt->alamatRt->kelurahan }}, {{ $warga->rt->alamatRt->kecamatan }}, {{ $warga->rt->alamatRt->kota }}, {{ $warga->rt->alamatRt->provinsi }} {{ $warga->rt->alamatRt->kode_pos }}</div>
                @else
                    <div class="text-amber-700 text-xs">Alamat RT belum dikonfigurasi. <a href="{{ route('alamat-rt.index') }}" class="underline">Konfigurasi</a></div>
                @endif
            </div>
            <div class="py-3 px-4 flex flex-col gap-1">
                <span class="text-slate-500">Alamat Detail (Domisili)</span>
                <span class="font-semibold text-slate-900">{{ $warga->alamat_detail ?? '-' }}</span>
            </div>
            <div class="py-3 px-4 flex flex-col gap-1">
                <span class="text-slate-500">Alamat KK</span>
                <span class="font-semibold text-slate-900">{{ $kk?->alamat_lengkap ?? '-' }}</span>
                <span class="text-[11px] text-slate-500">{{ $kk?->alamat ?? '-' }} @if($kk) — RT {{ $kk->rt }}/RW {{ $kk->rw }}, {{ $kk->desa }}, {{ $kk->kecamatan }} @endif</span>
            </div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Status Warga</span><span class="font-semibold text-slate-900">{{ $warga->status_warga }}</span></div>
        </div>

        {{-- Pane Keluarga --}}
        <div x-show="tab==='keluarga'" x-cloak class="divide-y divide-slate-100 text-xs">
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">No KK</span><div class="flex items-center gap-1.5">@if($kk)<span class="font-mono font-semibold text-slate-900">{{ $kk->no_kk }}</span><button onclick="navigator.clipboard.writeText('{{ $kk->no_kk }}');window.Toast&&Toast.success('No KK disalin')" class="text-slate-400 hover:text-slate-700"><i class="bi bi-copy text-[12px]"></i></button>@else<span class="text-slate-400">-</span>@endif</div></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Kepala Keluarga</span><span class="font-semibold text-slate-900">{{ $kk?->kepala_keluarga ?? '-' }}</span></div>
            <div class="py-3 px-4 flex justify-between items-center"><span class="text-slate-500">Hubungan</span><span class="font-semibold text-slate-900">{{ $warga->hubungan_keluarga ?? '-' }}</span></div>
            @if($kk && $kk->warga->count())
                <div class="p-4 bg-slate-50">
                    <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2">Anggota Keluarga ({{ $kk->warga->count() }} Jiwa)</p>
                    <div class="space-y-1.5">
                        @foreach($kk->warga as $a)
                            <div class="p-2.5 rounded border {{ $a->id==$warga->id ? 'border-teal-200 bg-teal-50' : 'border-slate-200 bg-white' }} flex justify-between items-center">
                                <div>
                                    <p class="font-semibold text-slate-900 text-xs">{{ $a->nama }} @if($a->id==$warga->id)<span class="text-teal-700 font-normal">(Data ini)</span>@endif @if($a->nama===$kk->kepala_keluarga)<span class="ml-1 px-1 py-0.5 bg-amber-100 text-amber-800 rounded text-[10px]">Kepala</span>@endif</p>
                                    <p class="text-[11px] text-slate-500">{{ $a->hubungan_keluarga ?? '-' }} • {{ $a->nik }}</p>
                                </div>
                                <a href="{{ route('warga.show', $a) }}" class="text-teal-700 hover:underline text-[11px] font-semibold">Lihat</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Mutasi --}}
    @if($warga->mutasi->count())
    <section class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 flex items-center gap-2">
            <i class="bi bi-arrow-left-right text-slate-500"></i><h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Riwayat Mutasi</h3>
            <span class="ml-auto text-xs px-2 py-0.5 rounded-full bg-slate-100">{{ $warga->mutasi->count() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50"><tr><th class="px-3 py-2 text-left font-semibold text-slate-600">Tanggal</th><th class="px-3 py-2 text-left font-semibold text-slate-600">Jenis</th><th class="px-3 py-2 text-left font-semibold text-slate-600">KK Lama</th><th class="px-3 py-2 text-left font-semibold text-slate-600">KK Baru</th><th class="px-3 py-2 text-left font-semibold text-slate-600">Keterangan</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($warga->mutasi as $m)<tr class="hover:bg-slate-50"><td class="px-3 py-2 font-mono">{{ $m->tanggal_mutasi->format('d/m/Y') }}</td><td class="px-3 py-2">{{ $m->label_jenis }}</td><td class="px-3 py-2 font-mono">{{ $m->kkLama->no_kk ?? '-' }}</td><td class="px-3 py-2 font-mono">{{ $m->kkBaru->no_kk ?? '-' }}</td><td class="px-3 py-2">{{ $m->keterangan ?? '-' }}</td></tr>@endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- Administrasi --}}
    <section class="bg-white border border-slate-200 rounded-lg p-3.5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-xs font-bold text-slate-900">Administrasi RT</h3>
            <p class="text-[11px] text-slate-500 mt-1">Cetak pengantar atau kelola data warga ini</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('warga.edit', $warga) }}" class="px-3.5 py-2 rounded-md bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold inline-flex items-center gap-1.5"><i class="bi bi-pencil"></i> Edit Warga</a>
            <a href="{{ route('warga.index') }}" class="px-3.5 py-2 rounded-md border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 inline-flex items-center gap-1.5"><i class="bi bi-list"></i> Daftar</a>
        </div>
    </section>
</div>
@endsection
