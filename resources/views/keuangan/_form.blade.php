{{--
    Form transaksi keuangan — dipakai di halaman create/edit pemasukan &
    pengeluaran maupun modal di kedua index.
    $keuangan null = mode tambah; terisi = mode edit (halaman edit).
    Di modal edit pada index, field diisi via JS (lihat fillPemasukanEditModal
    / fillPengeluaranEditModal).
--}}
@props([
    'action' => '',
    'method' => 'POST',
    'modal' => 'create-pemasukan',
    'keuangan' => null,
    'categories' => [],
    'submitLabel' => 'Simpan',
    'submitClass' => 'bg-green-600',
    'prefix' => 'trx',
    'formId' => null,
    'cancelUrl' => null,
    'jenisDefault' => 'PEMASUKAN',
    'showJenisSelect' => false,
])

@php
    $v = fn($key, $default = null) => old($key, $keuangan?->$key ?? $default);
    $tanggalVal = old('tanggal', $keuangan?->tanggal?->format('Y-m-d') ?? date('Y-m-d'));
    $jenisVal = old('jenis', $keuangan?->jenis ?? $jenisDefault);
@endphp

<form @if($formId)id="{{ $formId }}"@endif method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if(strtoupper($method) !== 'POST') @method($method) @endif
    <input type="hidden" name="_modal" value="{{ $modal }}">
    @if(str_starts_with($modal, 'edit-'))
        <input type="hidden" name="_modal_id" id="{{ $prefix }}ModalId" value="{{ $keuangan?->id }}">
    @endif
    @if(!$showJenisSelect)
        <input type="hidden" name="jenis" value="{{ $jenisDefault }}">
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Tanggal *</label>
            <input type="date" name="tanggal" id="{{ $prefix }}Tanggal" value="{{ $tanggalVal }}" class="w-full px-3 py-2 border rounded-md text-sm" required>
            @error('tanggal')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        @if($showJenisSelect)
        <div>
            <label class="block text-sm font-semibold mb-1">Jenis *</label>
            <select name="jenis" id="{{ $prefix }}Jenis" class="w-full px-3 py-2 border rounded-md text-sm">
                <option value="PEMASUKAN" {{ $jenisVal=='PEMASUKAN'?'selected':'' }}>Pemasukan</option>
                <option value="PENGELUARAN" {{ $jenisVal=='PENGELUARAN'?'selected':'' }}>Pengeluaran</option>
            </select>
            @error('jenis')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        @endif
        <div>
            <label class="block text-sm font-semibold mb-1">Kategori *</label>
            <input type="text" list="{{ $prefix }}CatList" name="kategori" id="{{ $prefix }}Kategori" value="{{ $v('kategori') }}" placeholder="{{ $jenisDefault === 'PEMASUKAN' ? 'Iuran Warga, Donasi...' : 'Konsumsi, Perbaikan...' }}" class="w-full px-3 py-2 border rounded-md text-sm" required>
            <datalist id="{{ $prefix }}CatList">@foreach($categories as $c)<option value="{{ $c }}">@endforeach</datalist>
            @error('kategori')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Jumlah (Rp) *</label>
            <x-rupiah-input name="jumlah" id="{{ $prefix }}Jumlah" :value="$v('jumlah')" required :min="0" />
            @error('jumlah')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Sumber Dana</label>
            <input type="text" name="sumber_dana" id="{{ $prefix }}Sumber" value="{{ $v('sumber_dana') }}" placeholder="Dana Lingkungan{{ $jenisDefault === 'PEMASUKAN' ? ', Donatur...' : '' }}" class="w-full px-3 py-2 border rounded-md text-sm">
            @error('sumber_dana')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-1">Keterangan</label>
            <input type="text" name="keterangan" id="{{ $prefix }}Keterangan" value="{{ $v('keterangan') }}" class="w-full px-3 py-2 border rounded-md text-sm">
            @error('keterangan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="flex justify-end gap-2 pt-4">
        @if($cancelUrl)
            <a href="{{ $cancelUrl }}" class="px-4 py-2 border rounded-md text-sm">Batal</a>
        @else
            <button type="button" data-close-modal class="px-4 py-2 border rounded-md text-sm">Batal</button>
        @endif
        <button type="submit" class="px-6 py-2 {{ $submitClass }} text-white rounded-md text-sm font-semibold">{{ $submitLabel }}</button>
    </div>
</form>
