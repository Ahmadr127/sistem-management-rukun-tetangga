{{--
    Form jenis kas — dipakai di halaman create/edit maupun modal di index.
    $kasJenis null = mode tambah; terisi = mode edit (halaman edit).
    Di modal edit pada index, field diisi via JS (lihat fillKasEditModal).
--}}
@props([
    'action' => '',
    'method' => 'POST',
    'modal' => 'create-kas',
    'kasJenis' => null,
    'rts' => [],
    'submitLabel' => 'Simpan',
    'prefix' => 'kas',
    'formId' => null,
    'cancelUrl' => null,
    'defaults' => [],
])

@php
    $v = fn($key, $default = null) => old($key, $kasJenis?->$key ?? $defaults[$key] ?? $default);
@endphp

<form @if($formId)id="{{ $formId }}"@endif method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if(strtoupper($method) !== 'POST') @method($method) @endif
    <input type="hidden" name="_modal" value="{{ $modal }}">
    @if($modal === 'edit-kas')
        <input type="hidden" name="_modal_id" id="{{ $prefix }}ModalId" value="{{ $kasJenis?->id }}">
    @endif

    @if(auth()->user()->isSuperAdmin())
    <div>
        <label class="block text-sm font-semibold mb-1">RT *</label>
        <select name="rt_id" id="{{ $prefix }}Rt" class="w-full px-3 py-2 border rounded-md text-sm" required>
            @if(!$kasJenis)<option value="">-- Pilih RT --</option>@endif
            @foreach($rts as $rt)<option value="{{ $rt->id }}" {{ (string)$v('rt_id')===(string)$rt->id?'selected':'' }}>{{ $rt->kode_rt }} - {{ $rt->nama_rt }}</option>@endforeach
        </select>
        @error('rt_id')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
    </div>
    @else
    <div>
        <label class="block text-sm font-semibold mb-1">RT</label>
        <div id="{{ $prefix }}RtName" class="px-3 py-2 bg-green-50 border rounded-md text-sm font-semibold text-green-800">{{ $kasJenis?->rt?->kode_rt ?? auth()->user()->rt?->kode_rt }}</div>
        <input type="hidden" name="rt_id" id="{{ $prefix }}RtHidden" value="{{ $v('rt_id', $kasJenis?->rt_id ?? auth()->user()->rt_id) }}">
    </div>
    @endif

    <div>
        <label class="block text-sm font-semibold mb-1">Nama Jenis Kas *</label>
        <input type="text" name="nama" id="{{ $prefix }}Nama" value="{{ $v('nama') }}" placeholder="cth: Kas Ronda Bulanan" class="w-full px-3 py-2 border rounded-md text-sm" required>
        @error('nama')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Periode *</label>
            <select name="periode_type" id="{{ $prefix }}Periode" class="w-full px-3 py-2 border rounded-md text-sm" required>
                <option value="monthly" {{ $v('periode_type','monthly')=='monthly'?'selected':'' }}>Bulanan</option>
                <option value="yearly" {{ $v('periode_type')=='yearly'?'selected':'' }}>Tahunan</option>
                <option value="weekly" {{ $v('periode_type')=='weekly'?'selected':'' }}>Mingguan</option>
            </select>
            @error('periode_type')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Target Iuran *</label>
            <select name="target_type" id="{{ $prefix }}Target" class="w-full px-3 py-2 border rounded-md text-sm" required>
                <option value="kk" {{ $v('target_type','kk')=='kk'?'selected':'' }}>Per KK (Kepala Keluarga)</option>
                <option value="perorangan" {{ $v('target_type')=='perorangan'?'selected':'' }}>Perorangan (Per Warga)</option>
            </select>
            @error('target_type')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Nominal (Rp) *</label>
            <x-rupiah-input name="nominal" id="{{ $prefix }}Nominal" :value="$v('nominal', $kasJenis ? null : 20000)" required :min="0" />
            @error('nominal')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            @if(!$kasJenis)<p class="text-xs text-gray-500 mt-1">Nominal per sel tanggal yang dibayar.</p>@endif
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Status</label>
            <label class="flex items-center gap-2 px-3 py-2 border rounded-md text-sm cursor-pointer">
                <input type="checkbox" name="is_active" id="{{ $prefix }}Active" value="1" {{ $v('is_active', $kasJenis?->is_active ?? true) ? 'checked' : '' }} class="accent-green-600">
                Aktif (bisa dibayar)
            </label>
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold mb-1">Deskripsi</label>
        <textarea name="deskripsi" id="{{ $prefix }}Deskripsi" rows="2" placeholder="Keterangan tambahan (opsional)" class="w-full px-3 py-2 border rounded-md text-sm">{{ $v('deskripsi') }}</textarea>
    </div>

    @if($kasJenis)
    <p class="text-xs text-amber-600 -mt-2">Mengganti target akan membuat data pembayaran lama tidak tampil di tabel.</p>
    @endif

    <div class="flex gap-2 justify-end">
        @if($cancelUrl)
            <a href="{{ $cancelUrl }}" class="px-4 py-2 text-sm bg-gray-200 rounded-md">Batal</a>
        @else
            <button type="button" data-close-modal class="px-4 py-2 text-sm bg-gray-200 rounded-md">Batal</button>
        @endif
        <button type="submit" class="px-4 py-2 text-sm bg-green-600 text-white rounded-md font-semibold">{{ $submitLabel }}</button>
    </div>
</form>
