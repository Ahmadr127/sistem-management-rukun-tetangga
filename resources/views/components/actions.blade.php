@props([
    'align' => 'right',
    'triggerIcon' => 'bi-three-dots-vertical',
    'menuClass' => 'w-40',
    'count' => 0,
])

{{--
    Kolom aksi. Jika jumlah aksi <=3, tampilkan sebagai icon button biasa;
    jika >3, gunakan dropdown titik tiga.
    $count harus di-passing dari parent (jumlah aksi maksimal).
    Fallback: jika count tidak di-passing (0), otomatis coba hitung dari slot
    dengan substring count sebagai fallback aman -> anggap <=3 (icon).
--}}
@php
    // Auto-count jika tidak diberikan: hitung action-item di slot HTML
    if ((int)$count === 0) {
        try {
            $html = (string) $slot;
            $auto = substr_count($html, 'action-item');
            if ($auto > 0) $count = $auto;
            else $count = 3;
        } catch (\Throwable $e) {
            $count = 3;
        }
    }
@endphp
@if((int)$count <= 3)
    <div class="flex items-center justify-start gap-1 actions-compact">
        {{ $slot }}
    </div>
    <style>
        .actions-compact .action-item { width: 1.75rem; height: 1.75rem; padding: 0; justify-content: center; border-radius: 0.375rem; border: 1px solid #e5e7eb; background: white; gap: 0; }
        .actions-compact .action-item:hover { background: rgba(0,119,116,0.1); border-color: rgba(0,119,116,0.3); color: #007774; }
        .actions-compact .action-form .action-item:hover { background: #fef2f2; border-color: #fecaca; color: #dc2626; }
        .actions-compact .action-label { display: none; }
        .actions-compact .action-form { display: inline-block; }
    </style>
@else
<div
    x-data="dropdownMenu({ align: '{{ $align }}' })"
    class="relative inline-block"
    @click.outside="menuOpen = false"
    @keydown.escape.window="menuOpen = false"
>
    <button
        type="button"
        x-ref="trigger"
        @click="toggle()"
        class="inline-flex items-center justify-center w-7 h-7 rounded-md text-sp-navy border border-gray-200 bg-white hover:bg-sp-primary/10 hover:border-sp-primary/30 hover:text-sp-primary transition-colors"
        title="Aksi"
    >
        <i class="bi {{ $triggerIcon }}"></i>
    </button>

    {{-- Dropdown menu: fixed positioning agar tidak terpotong overflow table --}}
    <div
        x-show="menuOpen"
        x-ref="menu"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click="menuOpen = false"
        :style="`top: ${position.top}px; left: ${position.left}px;`"
        class="fixed z-30 mt-1 {{ $menuClass }} bg-white border border-gray-200 rounded-lg shadow-lg py-1"
    >
        {{ $slot }}
    </div>
</div>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dropdownMenu', (config = {}) => ({
        menuOpen: false,
        align: config.align || 'right',
        position: { top: 0, left: 0 },

        toggle() {
            this.menuOpen = !this.menuOpen;
            if (this.menuOpen) {
                this.$nextTick(() => this.updatePosition());
            }
        },

        updatePosition() {
            const trigger = this.$refs.trigger.getBoundingClientRect();
            const menu = this.$refs.menu;
            const menuWidth = menu.offsetWidth || 160;

            let left = this.align === 'left' ? trigger.left : trigger.right - menuWidth;
            left = Math.min(Math.max(left, 8), window.innerWidth - menuWidth - 8);

            this.position = {
                top: trigger.bottom + 4,
                left: left,
            };
        },
    }));
});
</script>
@endpush
@endonce
@endif
