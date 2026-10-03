@extends('layouts.app')
@section('title', 'Peta KK')
@section('content')
<div class="w-full mx-auto max-w-6xl">
    {{-- Breadcrumb compact --}}
    <nav class="flex items-center gap-1.5 text-xs text-slate-500 mb-3">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700">Beranda</a>
        <span class="text-slate-400">/</span>
        <a href="{{ route('kartu-keluarga.index') }}" class="hover:text-slate-700">Kartu Keluarga</a>
        <span class="text-slate-400">/</span>
        <span class="text-slate-700 font-medium">Peta</span>
    </nav>

    <div class="bg-white border border-slate-200 rounded overflow-hidden">
        {{-- Header --}}
        <div class="px-4 sm:px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="min-w-0 flex-1">
                <h1 class="text-base font-semibold text-slate-900 tracking-tight">Peta Lokasi Rumah KK</h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    <span class="font-semibold text-sp-primary">{{ $markers->count() }}</span> dari
                    <span class="font-semibold">{{ $total }}</span> KK sudah memiliki koordinat
                    @if($selectedRtId && ($rt = $rts->firstWhere('id', (int) $selectedRtId)))
                        • {{ $rt->kode_rt }} - {{ $rt->nama_rt }}
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <button type="button" id="btn-my-location"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50">
                    <i class="bi bi-crosshair"></i> Lokasi Saya
                </button>
                <button type="button" id="btn-fit-bounds"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50">
                    <i class="bi bi-arrows-fullscreen"></i> Tampilkan Semua
                </button>
                <a href="{{ route('kartu-keluarga.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-sp-primary hover:bg-sp-primary-dark text-white text-xs font-semibold">
                    <i class="bi bi-list"></i> Data KK
                </a>
            </div>
        </div>

        {{-- Filter RT (superadmin) --}}
        @if($isSuper)
            <div class="px-4 sm:px-5 pb-4">
                <form method="GET" action="{{ route('kartu-keluarga.peta') }}" class="flex flex-wrap items-center gap-2">
                    <label for="filter-rt" class="text-xs font-semibold text-slate-600">Filter RT:</label>
                    <select id="filter-rt" name="rt_id" onchange="this.form.submit()"
                            class="px-3 py-1.5 border border-slate-300 rounded-md text-xs bg-white min-w-52">
                        <option value="">Semua RT</option>
                        @foreach($rts as $rt)
                            <option value="{{ $rt->id }}" {{ (int) $selectedRtId === (int) $rt->id ? 'selected' : '' }}>
                                {{ $rt->kode_rt }} - {{ $rt->nama_rt }}
                            </option>
                        @endforeach
                    </select>
                    @if($selectedRtId)
                        <a href="{{ route('kartu-keluarga.peta') }}" class="text-xs text-slate-400 hover:text-red-600 underline underline-offset-2">Reset</a>
                    @endif
                </form>
            </div>
        @endif

        @if($markers->isEmpty())
            <div class="px-4 sm:px-5 pb-6">
                <div class="border border-dashed border-slate-300 rounded-md p-8 text-center">
                    <i class="bi bi-geo-alt text-3xl text-slate-300"></i>
                    <p class="mt-2 text-sm font-semibold text-slate-700">Belum ada KK dengan koordinat</p>
                    <p class="mt-1 text-xs text-slate-500">Tambahkan koordinat rumah lewat form Tambah/Edit KK — via GPS, klik peta, atau ketik manual.</p>
                    <a href="{{ route('kartu-keluarga.index') }}" class="mt-3 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded bg-sp-primary hover:bg-sp-primary-dark text-white text-xs font-semibold">
                        Ke Data KK
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 border-t border-slate-200">
                {{-- Peta --}}
                <div class="lg:col-span-2 relative">
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
                    <div id="peta-kk-map" class="w-full h-[55vh] lg:h-[68vh] bg-slate-100 z-0"></div>
                </div>

                {{-- Daftar KK --}}
                <div class="border-t lg:border-t-0 lg:border-l border-slate-200 flex flex-col min-h-0">
                    <div class="p-3 border-b border-slate-200">
                        <div class="relative">
                            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" id="peta-kk-search" placeholder="Cari no KK / kepala keluarga..."
                                   class="w-full pl-8 pr-3 py-1.5 text-xs border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sp-primary/20 focus:border-sp-primary">
                        </div>
                    </div>
                    <div id="peta-kk-list" class="divide-y divide-slate-100 overflow-y-auto max-h-72 lg:max-h-[calc(68vh-57px)]">
                        @foreach($markers as $m)
                            <button type="button" data-marker-id="{{ $m['id'] }}"
                                    data-search="{{ strtolower($m['no_kk'] . ' ' . $m['kepala']) }}"
                                    class="peta-kk-item w-full text-left px-3 py-2.5 hover:bg-teal-50/60 flex items-start gap-2.5">
                                <span class="mt-0.5 w-6 h-6 shrink-0 rounded-full bg-sp-primary/10 text-sp-primary flex items-center justify-center">
                                    <i class="bi bi-house-door text-xs"></i>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-xs font-semibold text-slate-900 truncate">{{ $m['kepala'] }}</span>
                                    <span class="block font-mono text-[11px] text-slate-500">{{ $m['no_kk'] }}</span>
                                    <span class="block text-[11px] text-slate-400 truncate">{{ $m['rt'] }} • {{ $m['alamat'] }}</span>
                                </span>
                                <i class="bi bi-chevron-right text-slate-300 text-xs mt-1 shrink-0"></i>
                            </button>
                        @endforeach
                    </div>
                    <div class="px-3 py-2 border-t border-slate-200 text-[11px] text-slate-400">
                        <span id="peta-kk-count">{{ $markers->count() }}</span> KK ditampilkan • klik daftar untuk menuju lokasi
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@if($markers->isNotEmpty())
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const MARKERS = {{ Js::from($markers) }};

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function popupHtml(m) {
        return '<div style="min-width:200px;max-width:260px">'
            + '<div style="font-size:11px;color:#64748b">Kartu Keluarga</div>'
            + '<div style="font-weight:700;font-size:13px;color:#0f172a">' + esc(m.kepala) + '</div>'
            + '<div style="font-family:monospace;font-size:11px;color:#475569;margin-top:2px">' + esc(m.no_kk) + '</div>'
            + '<div style="font-size:11px;color:#64748b;margin-top:4px">' + esc(m.alamat) + '</div>'
            + '<div style="font-size:11px;color:#64748b">RT ' + esc(m.rt) + ' • ' + m.lat.toFixed(7) + ', ' + m.lng.toFixed(7) + '</div>'
            + '<a href="' + esc(m.url) + '" style="display:inline-block;margin-top:6px;font-size:11px;font-weight:700;color:#007774">Lihat Detail →</a>'
            + '</div>';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const mapEl = document.getElementById('peta-kk-map');
        if (!mapEl || typeof L === 'undefined') {
            if (mapEl) mapEl.innerHTML = '<div class="flex items-center justify-center h-full text-xs text-red-500 p-4 text-center">Peta gagal dimuat (CDN Leaflet tidak terjangkau).</div>';
            return;
        }

        const map = L.map(mapEl).setView([-6.2, 106.8167], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        const leafletMarkers = {};
        const bounds = L.latLngBounds();

        MARKERS.forEach(function (m) {
            const mk = L.marker([m.lat, m.lng]).addTo(map).bindPopup(popupHtml(m));
            leafletMarkers[m.id] = mk;
            bounds.extend([m.lat, m.lng]);
        });

        function fitAll() {
            if (MARKERS.length === 1) map.setView([MARKERS[0].lat, MARKERS[0].lng], 16);
            else map.fitBounds(bounds.pad(0.15));
        }
        fitAll();
        setTimeout(function () { map.invalidateSize(); fitAll(); }, 300);

        document.getElementById('btn-fit-bounds').addEventListener('click', fitAll);

        document.getElementById('btn-my-location').addEventListener('click', function () {
            if (!navigator.geolocation) {
                if (window.Toast) window.Toast.error('Browser tidak mendukung GPS.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    const p = [pos.coords.latitude, pos.coords.longitude];
                    L.circleMarker(p, { radius: 8, color: '#2563eb', fillColor: '#3b82f6', fillOpacity: 0.8 })
                        .addTo(map).bindPopup('Lokasi Anda saat ini').openPopup();
                    map.setView(p, 15);
                },
                function () {
                    if (window.Toast) window.Toast.error('Gagal mendapatkan lokasi.');
                },
                { enableHighAccuracy: true, timeout: 15000 }
            );
        });

        // Klik daftar -> terbang ke marker + buka popup
        document.querySelectorAll('.peta-kk-item').forEach(function (el) {
            el.addEventListener('click', function () {
                const id = Number(el.dataset.markerId);
                const mk = leafletMarkers[id];
                const m = MARKERS.find(function (x) { return x.id === id; });
                if (mk && m) {
                    map.flyTo([m.lat, m.lng], Math.max(map.getZoom(), 16), { duration: 0.8 });
                    setTimeout(function () { mk.openPopup(); }, 850);
                }
            });
        });

        // Cari daftar (filter list + marker)
        const searchInput = document.getElementById('peta-kk-search');
        const countEl = document.getElementById('peta-kk-count');
        searchInput.addEventListener('input', function () {
            const q = searchInput.value.toLowerCase().trim();
            let shown = 0;
            document.querySelectorAll('.peta-kk-item').forEach(function (el) {
                const hit = !q || (el.dataset.search || '').includes(q);
                el.style.display = hit ? '' : 'none';
                const mk = leafletMarkers[Number(el.dataset.markerId)];
                if (mk) {
                    if (hit && !map.hasLayer(mk)) mk.addTo(map);
                    if (!hit && map.hasLayer(mk)) map.removeLayer(mk);
                }
                if (hit) shown++;
            });
            if (countEl) countEl.textContent = shown;
        });
    });
})();
</script>
@endpush
@endif
@endsection
