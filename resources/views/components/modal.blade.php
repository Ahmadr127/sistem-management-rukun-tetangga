{{--
    Modal Component (vanilla JS, tanpa Alpine)

    Usage:
    <button type="button" data-open-modal="modal-id">Buka</button>

    <x-modal id="modal-id" title="Judul Modal" maxWidth="max-w-2xl">
        ... isi ...
    </x-modal>
--}}

@props([
    'id',
    'title',
    'maxWidth' => 'max-w-lg',
])

<div id="{{ $id }}" data-modal class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/40" data-modal-backdrop></div>
    <div class="relative bg-white rounded-lg shadow-xl w-full {{ $maxWidth }} max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 sticky top-0 bg-white rounded-t-lg z-10">
            <h3 class="font-bold text-gray-900">{{ $title }}</h3>
            <button type="button" data-close-modal class="w-8 h-8 inline-flex items-center justify-center rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100" title="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="p-5">
            {{ $slot }}
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
function openModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}
function closeModal(id) {
    const m = typeof id === 'string' ? document.getElementById(id) : id;
    if (!m) return;
    m.classList.add('hidden');
    if (!document.querySelector('[data-modal]:not(.hidden)')) {
        document.body.classList.remove('overflow-hidden');
    }
}
document.addEventListener('click', function (e) {
    const opener = e.target.closest('[data-open-modal]');
    if (opener) {
        openModal(opener.dataset.openModal);
        return;
    }
    if (e.target.closest('[data-close-modal]')) {
        closeModal(e.target.closest('[data-modal]'));
        return;
    }
    if (e.target.matches && e.target.matches('[data-modal-backdrop]')) {
        closeModal(e.target.closest('[data-modal]'));
    }
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[data-modal]:not(.hidden)').forEach(function (m) {
            m.classList.add('hidden');
        });
        document.body.classList.remove('overflow-hidden');
    }
});
</script>
@endpush
@endonce
