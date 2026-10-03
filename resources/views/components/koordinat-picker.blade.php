{{--
    Koordinat Picker Component (Leaflet + OpenStreetMap, tanpa API key)

    Mode input (default):
    - Tombol "Lokasi Saat Ini" (GPS browser)
    - Klik peta / geser marker untuk posisi manual
    - Input latitude/longitude bisa diketik manual

    Mode readonly (:readonly="true"): peta + titik saja + link Google Maps.

    Usage:
    <x-koordinat-picker :latitude="old('latitude', $kk->latitude ?? null)"
                        :longitude="old('longitude', $kk->longitude ?? null)" />

    <x-koordinat-picker :latitude="$kk->latitude" :longitude="$kk->longitude"
                        :readonly="true" height="260px" />
--}}

@props([
    'latitude' => null,
    'longitude' => null,
    'readonly' => false,
    'height' => '320px',
])

@php
    $hasKoordinat = $latitude !== null && $latitude !== '' && $longitude !== null && $longitude !== '';
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<div class="koordinat-picker"
     data-lat="{{ $latitude }}"
     data-lng="{{ $longitude }}"
     data-readonly="{{ $readonly ? '1' : '0' }}">

    @if(!$readonly)
        <div class="flex flex-wrap items-center gap-2 mb-2">
            <button type="button" data-gps
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-sp-primary hover:bg-sp-primary-dark text-white text-xs font-semibold disabled:opacity-60">
                <i class="bi bi-crosshair"></i>
                <span data-gps-label>Gunakan Lokasi Saat Ini</span>
            </button>
            <button type="button" data-clear
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-slate-300 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50">
                <i class="bi bi-eraser"></i>
                Hapus
            </button>
            <span class="text-xs text-slate-400">Klik peta atau geser pin untuk posisi manual</span>
        </div>
    @endif

    <div class="kp-map rounded-md border border-slate-300 overflow-hidden bg-slate-100" style="height: {{ $height }};"></div>

    <div class="grid grid-cols-2 gap-2 mt-2">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Latitude</label>
            <input type="text" name="latitude" value="{{ $latitude }}" inputmode="decimal" placeholder="-6.2000000"
                   {{ $readonly ? 'readonly tabindex="-1"' : '' }}
                   class="w-full px-3 py-2 border rounded-md text-sm font-mono {{ $readonly ? 'bg-slate-50 text-slate-700' : 'bg-white' }}">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">Longitude</label>
            <input type="text" name="longitude" value="{{ $longitude }}" inputmode="decimal" placeholder="106.8166667"
                   {{ $readonly ? 'readonly tabindex="-1"' : '' }}
                   class="w-full px-3 py-2 border rounded-md text-sm font-mono {{ $readonly ? 'bg-slate-50 text-slate-700' : 'bg-white' }}">
        </div>
    </div>

    @if($readonly && $hasKoordinat)
        <a href="https://www.google.com/maps?q={{ $latitude }},{{ $longitude }}" target="_blank" rel="noopener"
           class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-sp-primary hover:underline">
            <i class="bi bi-box-arrow-up-right"></i>
            Buka di Google Maps
        </a>
    @endif
</div>

@once
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.koordinat-picker:not([data-done])').forEach(initKoordinatPicker);
});

function initKoordinatPicker(root) {
    root.dataset.done = '1';

    if (typeof L === 'undefined') {
        const mapEl = root.querySelector('.kp-map');
        if (mapEl) mapEl.innerHTML = '<div class="flex items-center justify-center h-full text-xs text-red-500 p-4 text-center">Peta gagal dimuat (CDN Leaflet tidak terjangkau). Koordinat tetap bisa diisi manual.</div>';
        return;
    }

    const readonly = root.dataset.readonly === '1';
    const mapEl = root.querySelector('.kp-map');
    const latInput = root.querySelector('input[name="latitude"]');
    const lngInput = root.querySelector('input[name="longitude"]');
    const gpsBtn = root.querySelector('[data-gps]');
    const gpsLabel = root.querySelector('[data-gps-label]');
    const clearBtn = root.querySelector('[data-clear]');

    const DEFAULT_POS = { lat: -6.2000, lng: 106.8167, zoom: 12 }; // Jakarta
    const startLat = parseFloat(root.dataset.lat);
    const startLng = parseFloat(root.dataset.lng);
    const hasInitial = Number.isFinite(startLat) && Number.isFinite(startLng);

    const map = L.map(mapEl).setView(
        hasInitial ? [startLat, startLng] : [DEFAULT_POS.lat, DEFAULT_POS.lng],
        hasInitial ? 16 : 5
    );
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    let marker = null;

    function syncInputs(la, ln) {
        if (latInput) latInput.value = Number(la).toFixed(7);
        if (lngInput) lngInput.value = Number(ln).toFixed(7);
    }

    function setMarker(la, ln, moveView) {
        if (marker) {
            marker.setLatLng([la, ln]);
        } else {
            marker = L.marker([la, ln], { draggable: !readonly }).addTo(map);
            if (!readonly) {
                marker.on('dragend', function () {
                    const p = marker.getLatLng();
                    syncInputs(p.lat, p.lng);
                });
            }
        }
        if (moveView) map.setView([la, ln], Math.max(map.getZoom(), 16));
        syncInputs(la, ln);
    }

    function clearMarker() {
        if (marker) {
            map.removeLayer(marker);
            marker = null;
        }
        if (latInput) latInput.value = '';
        if (lngInput) lngInput.value = '';
    }

    function notifyOk(msg) {
        if (window.Toast) window.Toast.success(msg);
    }

    function notifyErr(msg) {
        if (window.Toast) window.Toast.error(msg, { duration: 6000 });
        else alert(msg);
    }

    if (hasInitial) setMarker(startLat, startLng, false);

    if (!readonly) {
        map.on('click', function (e) {
            setMarker(e.latlng.lat, e.latlng.lng, false);
        });

        [latInput, lngInput].forEach(function (el) {
            if (!el) return;
            el.addEventListener('change', function () {
                const la = parseFloat(latInput.value);
                const ln = parseFloat(lngInput.value);
                if (Number.isFinite(la) && Number.isFinite(ln) && Math.abs(la) <= 90 && Math.abs(ln) <= 180) {
                    setMarker(la, ln, true);
                } else {
                    notifyErr('Koordinat tidak valid (latitude -90..90, longitude -180..180).');
                }
            });
        });

        if (gpsBtn) {
            gpsBtn.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    notifyErr('Browser tidak mendukung GPS.');
                    return;
                }
                const original = gpsLabel ? gpsLabel.textContent : '';
                gpsBtn.disabled = true;
                if (gpsLabel) gpsLabel.textContent = 'Mencari lokasi...';
                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        setMarker(pos.coords.latitude, pos.coords.longitude, true);
                        gpsBtn.disabled = false;
                        if (gpsLabel) gpsLabel.textContent = original;
                        notifyOk('Lokasi saat ini berhasil dipasang.');
                    },
                    function (err) {
                        gpsBtn.disabled = false;
                        if (gpsLabel) gpsLabel.textContent = original;
                        notifyErr(err && err.code === 1
                            ? 'Izin lokasi ditolak. Aktifkan izin lokasi di browser lalu coba lagi.'
                            : 'Gagal mendapatkan lokasi. Coba lagi atau geser peta manual.');
                    },
                    { enableHighAccuracy: true, timeout: 15000, maximumAge: 60000 }
                );
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', clearMarker);
        }

        setTimeout(function () { map.invalidateSize(); }, 300);
    }
}
</script>
@endpush
@endonce
