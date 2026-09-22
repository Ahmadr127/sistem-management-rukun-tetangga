@extends('layouts.app')
@section('title', 'Tabel ' . $kasJenis->nama)
@section('content')
<div class="w-full mx-auto space-y-4">
    @php
        $canManage = auth()->user()->hasPermission('manage_kas');
        $isKk = $kasJenis->isKk();
        $qBase = ['mode' => $mode, 'bulan' => request('bulan'), 'minggu' => request('minggu'), 'tahun' => request('tahun'), 'search' => request('search')];
    @endphp

    {{-- Header jenis + statistik --}}
    <x-card padding="false" accent="green">
        <x-slot name="title">
            <a href="{{ route('kas-warga.index') }}" class="text-gray-400 hover:text-green-700 mr-1"><i class="bi bi-arrow-left"></i></a>
            {{ $kasJenis->nama }}
            <span class="ml-1 text-xs px-1.5 py-0.5 rounded font-normal @if($kasJenis->periode_type=='monthly') bg-blue-100 text-blue-800 @elseif($kasJenis->periode_type=='yearly') bg-orange-100 text-orange-800 @else bg-purple-100 text-purple-800 @endif">{{ $kasJenis->periode_label }}</span>
            <span class="text-xs px-1.5 py-0.5 rounded font-normal @if($isKk) bg-amber-100 text-amber-800 @else bg-slate-200 text-slate-700 @endif">{{ $isKk ? 'Per KK' : 'Perorangan' }}</span>
        </x-slot>
        <x-slot name="subtitle">Rp {{ number_format($kasJenis->nominal,0,',','.') }} per tanggal • {{ $kasJenis->rt->kode_rt ?? '' }} • Klik sel tanggal untuk input pembayaran</x-slot>
        <x-slot name="actions">
            @if($canManage)
            <a href="{{ route('kas-warga.edit', $kasJenis) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-md bg-amber-500 hover:bg-amber-600 text-white">
                <i class="bi bi-pencil"></i> Edit Jenis
            </a>
            @endif
        </x-slot>
        <div class="px-4 py-3 grid grid-cols-2 md:grid-cols-5 gap-3 bg-gray-50 border-b border-gray-100">
            <div class="bg-white border rounded-lg px-3 py-2">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">{{ $isKk ? 'Total KK' : 'Total Warga' }}</div>
                <div class="text-xl font-bold text-slate-800">{{ $stats['total_subjek'] }}</div>
            </div>
            <div class="bg-white border rounded-lg px-3 py-2">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">Sudah Bayar (sel)</div>
                <div class="text-xl font-bold text-green-700">{{ $stats['terbayar'] }}</div>
            </div>
            <div class="bg-white border rounded-lg px-3 py-2">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">Belum Bayar (sel)</div>
                <div class="text-xl font-bold text-amber-600">{{ $stats['belum'] }}</div>
            </div>
            <div class="bg-white border rounded-lg px-3 py-2">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">Terkumpul</div>
                <div class="text-xl font-bold text-green-700">Rp {{ number_format($stats['rupiah'],0,',','.') }}</div>
            </div>
            <div class="bg-white border rounded-lg px-3 py-2">
                <div class="text-[11px] font-semibold text-gray-500 uppercase">Progres {{ $nav['title'] }}</div>
                <div class="text-xl font-bold text-slate-800">{{ $stats['persen'] }}%</div>
                <div class="h-1.5 bg-gray-200 rounded-full mt-1 overflow-hidden"><div class="h-full bg-green-500 rounded-full" style="width:{{ $stats['persen'] }}%"></div></div>
            </div>
        </div>

        {{-- Tab mode + navigasi periode + cari --}}
        <div class="px-4 py-3 border-b border-gray-100 flex flex-wrap items-center gap-2">
            <div class="inline-flex rounded-md overflow-hidden border text-sm font-semibold">
                <a href="{{ route('kas-warga.show', array_merge(['kasJenis' => $kasJenis->id], ['mode' => 'bulan', 'search' => request('search')])) }}"
                   class="px-3 py-1.5 {{ $mode=='bulan' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-green-50' }}">Bulan ini</a>
                <a href="{{ route('kas-warga.show', array_merge(['kasJenis' => $kasJenis->id], ['mode' => 'minggu', 'search' => request('search')])) }}"
                   class="px-3 py-1.5 border-x {{ $mode=='minggu' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-green-50' }}">Minggu ini</a>
                <a href="{{ route('kas-warga.show', array_merge(['kasJenis' => $kasJenis->id], ['mode' => 'tahun', 'search' => request('search')])) }}"
                   class="px-3 py-1.5 {{ $mode=='tahun' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 hover:bg-green-50' }}">Tahun ini</a>
            </div>
            <div class="inline-flex items-center gap-1 text-sm">
                <a href="{{ route('kas-warga.show', array_merge(['kasJenis' => $kasJenis->id, 'mode' => $mode, 'search' => request('search')], $mode=='bulan' ? ['bulan' => $nav['prev']] : ($mode=='minggu' ? ['minggu' => $nav['prev']] : ['tahun' => $nav['prev']]))) }}"
                   class="px-2.5 py-1.5 border rounded-md bg-white hover:bg-gray-100" title="Sebelumnya"><i class="bi bi-chevron-left"></i></a>
                <span class="px-3 py-1.5 font-semibold text-slate-800 whitespace-nowrap">{{ $nav['title'] }}</span>
                <a href="{{ route('kas-warga.show', array_merge(['kasJenis' => $kasJenis->id, 'mode' => $mode, 'search' => request('search')], $mode=='bulan' ? ['bulan' => $nav['next']] : ($mode=='minggu' ? ['minggu' => $nav['next']] : ['tahun' => $nav['next']]))) }}"
                   class="px-2.5 py-1.5 border rounded-md bg-white hover:bg-gray-100" title="Berikutnya"><i class="bi bi-chevron-right"></i></a>
            </div>
            <form method="GET" action="{{ route('kas-warga.show', $kasJenis) }}" class="inline-flex items-center gap-1.5 text-sm">
                <input type="hidden" name="mode" value="{{ $mode }}">
                @if($mode=='bulan')
                    <input type="month" name="bulan" value="{{ request('bulan', $nav['current']) }}" class="px-2 py-1.5 border border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                @elseif($mode=='minggu')
                    <input type="date" name="minggu" value="{{ request('minggu', $nav['current']) }}" class="px-2 py-1.5 border border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                @else
                    <input type="number" name="tahun" value="{{ request('tahun', $nav['current']) }}" min="2000" max="2100" class="w-24 px-2 py-1.5 border border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                @endif
            </form>
            <form method="GET" action="{{ route('kas-warga.show', $kasJenis) }}" class="inline-flex items-center gap-1.5 text-sm ml-auto">
                <input type="hidden" name="mode" value="{{ $mode }}">
                @if($mode=='bulan')<input type="hidden" name="bulan" value="{{ request('bulan') }}">@endif
                @if($mode=='minggu')<input type="hidden" name="minggu" value="{{ request('minggu') }}">@endif
                @if($mode=='tahun')<input type="hidden" name="tahun" value="{{ request('tahun') }}">@endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $isKk ? 'Kepala keluarga / No KK...' : 'Nama / NIK...' }}" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm w-52">
                <button class="px-3 py-1.5 bg-green-600 text-white rounded-md"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <div class="px-4 py-2 flex flex-wrap gap-3 text-xs text-gray-600 bg-white border-b border-gray-100">
            <span class="inline-flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-green-500 inline-flex items-center justify-center text-white" style="font-size:9px"><i class="bi bi-check-lg"></i></span> Sudah bayar (klik untuk ubah / batalkan)</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-4 h-4 rounded border-2 border-dashed border-gray-300 inline-block"></span> Belum bayar (klik untuk input)</span>
        </div>

        {{-- Matriks --}}
        <div class="overflow-x-auto">
            <table class="border-collapse w-full text-sm" style="min-width: max-content">
                <thead class="sticky top-0">
                    <tr class="bg-slate-800 text-white">
                        <th class="sticky left-0 z-10 bg-slate-800 px-3 py-2 text-left font-semibold w-12">No</th>
                        <th class="sticky left-12 z-10 bg-slate-800 px-3 py-2 text-left font-semibold min-w-[190px]">{{ $isKk ? 'Kepala Keluarga' : 'Nama Warga' }}</th>
                        <th class="sticky bg-slate-800 px-3 py-2 text-left font-semibold min-w-[150px]" style="left: 238px">{{ $isKk ? 'No KK / NIK' : 'NIK' }}</th>
                        @foreach($columns as $col)
                        <th class="px-1 py-1.5 text-center font-semibold min-w-[46px] {{ ($col['is_today'] ?? false) ? 'bg-green-600' : '' }} {{ ($col['is_weekend'] ?? false) ? 'bg-slate-700' : '' }}" title="{{ $col['full'] }}">
                            <div class="text-[11px] leading-tight">{{ $col['label'] }}</div>
                            <div class="text-[9px] font-normal opacity-75 leading-tight">{{ $col['sub'] }}</div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                    @php
                        if ($isKk) {
                            $kepala = $row->anggota->firstWhere('hubungan_keluarga', 'Kepala Keluarga')
                                ?? $row->anggota->firstWhere('nama', $row->kepala_keluarga)
                                ?? $row->anggota->first();
                            $namaSubjek = $row->kepala_keluarga ?: '-';
                            $nikSubjek = $kepala->nik ?? '-';
                            $subId = $row->id;
                            $subKey = 'kk';
                        } else {
                            $namaSubjek = $row->nama;
                            $nikSubjek = $row->nik;
                            $subId = $row->id;
                            $subKey = 'warga';
                        }
                        $no = ($rows->currentPage()-1)*$rows->perPage() + $loop->iteration;
                    @endphp
                    <tr class="border-t border-gray-100 hover:bg-green-50/40">
                        <td class="sticky left-0 bg-white px-3 py-1.5">{{ $no }}</td>
                        <td class="sticky left-12 bg-white px-3 py-1.5 font-medium whitespace-nowrap">
                            {{ $namaSubjek }}
                            @if($isKk && $row->anggota->count())<span class="ml-1 text-[10px] px-1.5 py-0.5 bg-slate-100 rounded-full text-slate-600">{{ $row->anggota->count() }} jiwa</span>@endif
                        </td>
                        <td class="sticky bg-white px-3 py-1.5 font-mono text-xs whitespace-nowrap" style="left: 238px">
                            @if($isKk)<div>{{ $row->no_kk }}</div><div class="text-gray-500">NIK: {{ $nikSubjek }}</div>
                            @else{{ $nikSubjek }}@endif
                        </td>
                        @foreach($columns as $col)
                        @php
                            $lookupKey = $mode == 'tahun' ? $col['week'] : $col['key'];
                            $bayar = $payMap[$subId . '|' . $lookupKey] ?? null;
                        @endphp
                        <td class="px-1 py-1 text-center border-l border-gray-100">
                            @if($bayar)
                                @if($canManage)
                                <button type="button"
                                    class="w-9 h-9 rounded-md bg-green-500 hover:bg-green-600 text-white font-bold shadow-sm"
                                    title="Sudah bayar Rp {{ number_format($bayar->nominal_bayar,0,',','.') }} • {{ $bayar->waktu_bayar?->locale('id')->isoFormat('D MMM YYYY HH:mm') }} — klik untuk ubah/batalkan"
                                    data-paid="1"
                                    data-pembayaran-id="{{ $bayar->id }}"
                                    data-nominal="{{ $bayar->nominal_bayar }}"
                                    data-catatan="{{ $bayar->catatan }}"
                                    data-waktu="{{ $bayar->waktu_bayar?->locale('id')->isoFormat('dddd, D MMM YYYY HH:mm') }}"
                                    data-subject="{{ $namaSubjek }}"
                                    data-tanggal="{{ $col['key'] }}"
                                    data-tanggal-full="{{ $col['full'] }}"
                                    data-subject-id="{{ $subId }}"
                                    data-subject-key="{{ $subKey }}"
                                    onclick="openBayarModal(this)">✓</button>
                                @else
                                <span class="inline-flex w-9 h-9 rounded-md bg-green-500 text-white font-bold items-center justify-center shadow-sm" title="Sudah bayar Rp {{ number_format($bayar->nominal_bayar,0,',','.') }}">✓</span>
                                @endif
                            @else
                                @if($canManage)
                                <button type="button"
                                    class="w-9 h-9 rounded-md border-2 border-dashed border-gray-300 text-transparent hover:border-amber-500 hover:bg-amber-50 hover:text-amber-600 font-bold"
                                    title="Belum bayar — klik untuk input ({{ $col['full'] }})"
                                    data-paid="0"
                                    data-subject="{{ $namaSubjek }}"
                                    data-tanggal="{{ $col['key'] }}"
                                    data-tanggal-full="{{ $col['full'] }}"
                                    data-subject-id="{{ $subId }}"
                                    data-subject-key="{{ $subKey }}"
                                    onclick="openBayarModal(this)">+</button>
                                @else
                                <span class="inline-block w-9 h-9 rounded-md bg-gray-100"></span>
                                @endif
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr><td colspan="{{ 3 + count($columns) }}" class="px-3 py-8 text-center text-sm text-gray-500">
                        {{ $isKk ? 'Belum ada data KK di RT ini.' : 'Belum ada data warga aktif di RT ini.' }}
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $rows->links() }}
        </div>
    </x-card>
</div>

{{-- Modal input pembayaran --}}
@if($canManage)
<div id="bayarModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl">
        <h3 class="font-bold text-gray-900" id="bayarTitle">Input Pembayaran</h3>
        <p class="text-xs text-gray-500 mb-1" id="bayarSubject"></p>
        <p class="text-xs font-semibold text-green-700 mb-4" id="bayarTanggal"></p>
        <div id="bayarInfo" class="hidden mb-3 text-xs bg-green-50 border border-green-200 rounded-md px-3 py-2 text-green-800"></div>
        <form id="bayarForm" method="POST" action="{{ route('kas-warga.bayar', $kasJenis) }}" class="space-y-3">
            @csrf
            <input type="hidden" name="kartu_keluarga_id" id="f_kk">
            <input type="hidden" name="warga_id" id="f_warga">
            <input type="hidden" name="tanggal" id="f_tanggal">
            <input type="hidden" name="mode" value="{{ $mode }}">
            @if($mode=='bulan')<input type="hidden" name="bulan" value="{{ request('bulan') }}">@endif
            @if($mode=='minggu')<input type="hidden" name="minggu" value="{{ request('minggu') }}">@endif
            @if($mode=='tahun')<input type="hidden" name="tahun" value="{{ request('tahun') }}">@endif
            @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
            <div>
                <label class="block text-sm font-semibold mb-1">Nominal Bayar (Rp) *</label>
                <input type="number" name="nominal_bayar" id="f_nominal" min="0" required class="w-full px-3 py-2 border rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Waktu Bayar (otomatis)</label>
                <div class="px-3 py-2 bg-gray-50 border rounded-md text-sm text-gray-700 flex items-center gap-2">
                    <i class="bi bi-clock"></i><span id="jamOtomatis"></span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Catatan</label>
                <input type="text" name="catatan" id="f_catatan" maxlength="255" placeholder="Opsional" class="w-full px-3 py-2 border rounded-md text-sm">
            </div>
            <div class="flex gap-2 justify-between pt-2">
                <button type="button" id="btnBatalBayar" class="hidden px-4 py-1.5 text-sm bg-red-100 text-red-700 rounded-md font-semibold hover:bg-red-200">Batalkan Pembayaran</button>
                <div class="flex gap-2 ml-auto">
                    <button type="button" onclick="closeBayarModal()" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Batal</button>
                    <button type="submit" class="px-4 py-1.5 text-sm bg-green-600 text-white rounded-md font-semibold">Simpan</button>
                </div>
            </div>
        </form>
        <form id="hapusForm" method="POST" class="hidden">@csrf @method('DELETE')
            <input type="hidden" name="mode" value="{{ $mode }}">
        </form>
    </div>
</div>
@push('scripts')
<script>
function tickClock() {
    const el = document.getElementById('jamOtomatis');
    if (el) el.textContent = new Date().toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'short' }) + ' WIB';
}
setInterval(tickClock, 1000); tickClock();

function openBayarModal(btn) {
    const d = btn.dataset;
    const modal = document.getElementById('bayarModal');
    document.getElementById('bayarSubject').textContent = d.subject;
    document.getElementById('bayarTanggal').textContent = d.tanggalFull;
    document.getElementById('f_tanggal').value = d.tanggal;
    document.getElementById('f_kk').value = d.subjectKey === 'kk' ? d.subjectId : '';
    document.getElementById('f_warga').value = d.subjectKey === 'warga' ? d.subjectId : '';
    const info = document.getElementById('bayarInfo');
    const btnBatal = document.getElementById('btnBatalBayar');
    if (d.paid === '1') {
        document.getElementById('bayarTitle').textContent = 'Sudah Bayar — Ubah / Batalkan';
        document.getElementById('f_nominal').value = d.nominal;
        document.getElementById('f_catatan').value = d.catatan || '';
        info.classList.remove('hidden');
        info.textContent = 'Dibayar pada ' + d.waktu + ' sebesar Rp ' + Number(d.nominal).toLocaleString('id-ID') + '. Simpan untuk mengubah nominal, atau batalkan pembayaran.';
        btnBatal.classList.remove('hidden');
        btnBatal.onclick = function() {
            if (!confirm('Yakin batalkan pembayaran ini? Sel kembali menjadi belum bayar.')) return;
            const f = document.getElementById('hapusForm');
            const params = new URLSearchParams({ mode: '{{ $mode }}'@if(request('bulan')), bulan: '{{ request('bulan') }}'@endif @if(request('minggu')), minggu: '{{ request('minggu') }}'@endif @if(request('tahun')), tahun: '{{ request('tahun') }}'@endif @if(request('search')), search: @json(request('search'))@endif });
            f.action = "{{ url('kas-warga-bayar') }}/" + d.pembayaranId + '?' + params.toString();
            f.submit();
        };
    } else {
        document.getElementById('bayarTitle').textContent = 'Input Pembayaran';
        document.getElementById('f_nominal').value = {{ (float) $kasJenis->nominal }};
        document.getElementById('f_catatan').value = '';
        info.classList.add('hidden');
        btnBatal.classList.add('hidden');
    }
    modal.classList.remove('hidden');
}
function closeBayarModal() { document.getElementById('bayarModal').classList.add('hidden'); }
document.getElementById('bayarModal')?.addEventListener('click', function(e) { if (e.target === this) closeBayarModal(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeBayarModal(); });
</script>
@endpush
@endif
@endsection
