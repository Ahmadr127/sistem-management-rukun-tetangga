{{-- Form partial untuk create & edit. Variabel: $setting (null saat create). --}}
@php
    $isEdit = isset($setting) && $setting->exists;
    $lockKey = $isEdit; // key tidak boleh diubah setelah dibuat
    $lockType = $isEdit && $setting->is_system; // tipe pengaturan sistem dikunci
    $currentType = old('type', $isEdit ? $setting->type : 'text');
@endphp

<div class="grid grid-cols-1 gap-6" x-data="{ type: '{{ $currentType }}' }">
    <div>
        <label for="key" class="block text-sm font-medium text-gray-700">Key</label>
        <input type="text" name="key" id="key" value="{{ old('key', $isEdit ? $setting->key : '') }}"
               @if(!$isEdit) required @else disabled @endif
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm @if($isEdit) bg-gray-100 @endif"
               placeholder="contoh: site_name">
        @if($isEdit)
        <p class="mt-1 text-sm text-gray-500">Key tidak dapat diubah setelah dibuat.</p>
        @else
        <p class="mt-1 text-sm text-gray-500">Format snake_case, unik (contoh: site_name, site_logo).</p>
        @endif
        @error('key')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="display_name" class="block text-sm font-medium text-gray-700">Nama Pengaturan</label>
        <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $isEdit ? $setting->display_name : '') }}" required
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
               placeholder="contoh: Nama Sistem">
        @error('display_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="type" class="block text-sm font-medium text-gray-700">Tipe Nilai</label>
        <select name="type" id="type" x-model="type" @if($lockType) disabled @endif
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm @if($lockType) bg-gray-100 @endif">
            <option value="text" @selected($currentType === 'text')>Teks singkat</option>
            <option value="textarea" @selected($currentType === 'textarea')>Teks panjang</option>
            <option value="image" @selected($currentType === 'image')>Gambar / Logo</option>
        </select>
        @if($lockType)
        <input type="hidden" name="type" value="{{ $setting->type }}">
        <p class="mt-1 text-sm text-gray-500">Tipe pengaturan sistem dikunci.</p>
        @endif
        @error('type')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nilai teks --}}
    <div x-show="type !== 'image'">
        <div x-show="type === 'text'">
            <label for="value" class="block text-sm font-medium text-gray-700">Nilai</label>
            <input type="text" name="value" id="value" value="{{ old('value', $isEdit && !$setting->isImage() ? $setting->value : '') }}" :disabled="type !== 'text'"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                   placeholder="contoh: Sistem Manajemen Rukun Tetangga">
        </div>
        <div x-show="type === 'textarea'" x-cloak>
            <label for="value_area" class="block text-sm font-medium text-gray-700">Nilai</label>
            <textarea name="value" id="value_area" rows="4" :disabled="type !== 'textarea'"
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                      placeholder="Tulis nilai pengaturan...">{{ old('value', $isEdit && !$setting->isImage() ? $setting->value : '') }}</textarea>
        </div>
        @error('value')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Upload gambar --}}
    <div x-show="type === 'image'" x-cloak>
        @if($isEdit && $setting->isImage() && $setting->valueUrl())
        <div class="mb-2">
            <p class="block text-sm font-medium text-gray-700 mb-1">Logo saat ini</p>
            <img src="{{ $setting->valueUrl() }}" alt="Logo saat ini" class="h-20 w-auto object-contain bg-gray-50 border rounded p-1">
        </div>
        @endif
        <label for="logo" class="block text-sm font-medium text-gray-700">File Logo</label>
        <input type="file" name="logo" id="logo" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:bg-green-600 file:text-white file:font-semibold hover:file:bg-green-700"
               onchange="previewLogo(this)">
        <p class="mt-1 text-sm text-gray-500">PNG/JPG/SVG/WebP, maks 2MB.</p>
        <div id="logoPreview" class="hidden mt-2">
            <p class="block text-sm font-medium text-gray-700 mb-1">Pratinjau baru</p>
            <img id="logoPreviewImg" alt="Pratinjau logo" class="h-20 w-auto object-contain bg-gray-50 border rounded p-1">
        </div>
        <script>
        function previewLogo(input) {
            var file = input.files && input.files[0];
            if (!file) return;
            var show = function (src) {
                var box = document.getElementById('logoPreview');
                var img = document.getElementById('logoPreviewImg');
                if (!box || !img) return;
                img.src = src;
                box.classList.remove('hidden');
            };
            try {
                // Cara utama; dibungkus try-catch karena sebagian ekstensi browser
                // dapat merusak/menimpa window.URL sehingga createObjectURL gagal.
                if (window.URL && typeof window.URL.createObjectURL === 'function') {
                    show(window.URL.createObjectURL(file));
                    return;
                }
                throw new Error('no-createObjectURL');
            } catch (e) {
                // Cadangan: FileReader (tidak bergantung pada window.URL)
                try {
                    var reader = new FileReader();
                    reader.onload = function (ev) { show(ev.target.result); };
                    reader.readAsDataURL(file);
                } catch (e2) { /* pratinjau dilewati, form tetap bisa disimpan */ }
            }
        }
        </script>
        @error('logo')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea name="description" id="description" rows="2"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                  placeholder="Keterangan singkat...">{{ old('description', $isEdit ? $setting->description : '') }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
