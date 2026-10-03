{{--
    Rupiah Input Component — input nominal dengan format ribuan otomatis.

    Tampil: "20.000.000" (dengan prefix Rp).
    Terkirim: angka murni "20000000" via hidden input (aman untuk validasi numeric).

    Usage:
    <x-rupiah-input name="nominal" id="formNominal" :value="old('nominal', 20000)" required />

    Isi via JS (mis. modal edit):
    Rupiah.setValue('formNominal', 20000);
--}}

@props([
    'name',
    'value' => null,
    'id' => null,
    'placeholder' => '0',
    'required' => false,
    'min' => 0,
])

@php $rid = $id ?? 'rupiah-' . Str::random(8); @endphp

<div class="relative">
    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-gray-500 pointer-events-none">Rp</span>
    <input type="text" inputmode="numeric" autocomplete="off"
           id="{{ $rid }}" data-rupiah data-target="{{ $rid }}-raw"
           value="{{ $value }}" placeholder="{{ $placeholder }}"
           @if($required)required @endif
           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
    <input type="hidden" name="{{ $name }}" id="{{ $rid }}-raw" value="">
</div>

@once
@push('scripts')
<script>
window.Rupiah = {
    digits(v) {
        return String(v ?? '').replace(/\D/g, '');
    },
    sync(display) {
        const raw = document.getElementById(display.dataset.target);
        const d = this.digits(display.value).replace(/^0+(?=\d)/, '');
        display.value = d ? Number(d).toLocaleString('id-ID') : '';
        if (raw) raw.value = d;
    },
    setValue(displayId, v) {
        const display = document.getElementById(displayId);
        if (!display) return;
        display.value = v ?? '';
        this.sync(display);
    },
    init() {
        document.querySelectorAll('input[data-rupiah]').forEach(function (el) {
            if (el.dataset.rupiahInit) return;
            el.dataset.rupiahInit = '1';
            el.addEventListener('input', function () { window.Rupiah.sync(el); });
            window.Rupiah.sync(el);
        });
    }
};
document.addEventListener('DOMContentLoaded', function () {
    window.Rupiah.init();
});
</script>
@endpush
@endonce
