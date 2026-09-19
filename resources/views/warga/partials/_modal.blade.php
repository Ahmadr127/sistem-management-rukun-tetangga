{{-- Reusable Warga Modal: expects $kartuKeluarga (optional), $rts, $action, $method, $warga (optional for edit), $modalId --}}
@php
    $kartuKeluarga = $kartuKeluarga ?? null;
    $rts = $rts ?? null;
    $action = $action ?? '';
    $method = $method ?? 'POST';
    $warga = $warga ?? null;
    $modalId = $modalId ?? 'wargaModal';
    $title = $title ?? 'Form Warga';
    $isEdit = $warga && $warga->exists;
    $selectedRtId = old('rt_id', $warga?->rt_id ?? $kartuKeluarga?->rt_id ?? auth()->user()->rt_id);
    $selectedKkId = old('kartu_keluarga_id', $warga?->kartu_keluarga_id ?? $kartuKeluarga?->id);
    if (!$rts) $rts = \App\Models\Rt::active()->orderBy('kode_rt')->get();
@endphp
<div x-data="{ open: false }" x-init="
    $watch('open', v => document.body.style.overflow = v ? 'hidden' : '');
    // auto open if has validation errors and this modal is the intended one
    @if($errors->any() && old('kartu_keluarga_id') == $selectedKkId && (($isEdit && old('_modal')==$modalId) || (!$isEdit && old('_modal')==$modalId)))
        open = true;
    @endif
">
    {{-- Trigger slot --}}
    <div @click="open = true" class="inline-flex cursor-pointer">
        {!! $trigger ?? '' !!}
    </div>

    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/50" @click="open=false"></div>
            <div x-show="open" x-transition.scale class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 class="text-lg font-bold text-gray-800">{{ $title }}</h3>
                    <button @click="open=false" class="p-2 rounded-full hover:bg-gray-100"><i class="bi bi-x-lg"></i></button>
                </div>
                <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto">
                    @csrf
                    @if(strtoupper($method) !== 'POST') @method($method) @endif
                    <input type="hidden" name="_modal" value="{{ $modalId }}">
                    {{-- redirect back to KK show if from KK --}}
                    @if($kartuKeluarga)
                        <input type="hidden" name="redirect_to" value="{{ route('kartu-keluarga.show', $kartuKeluarga) }}">
                        <input type="hidden" name="kartu_keluarga_id" value="{{ $kartuKeluarga->id }}">
                        <input type="hidden" name="rt_id" value="{{ $kartuKeluarga->rt_id }}">
                    @endif
                    <div class="p-6 space-y-4">
                        @if($kartuKeluarga)
                            <div class="p-3 bg-blue-50 border border-blue-100 rounded text-sm">
                                <span class="font-semibold">KK:</span> <span class="font-mono">{{ $kartuKeluarga->no_kk }}</span> — {{ $kartuKeluarga->kepala_keluarga ?? '-' }}
                                <span class="ml-3 px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-xs">RT {{ $kartuKeluarga->rtRelation?->kode_rt ?? $kartuKeluarga->rt ?? '-' }}</span>
                            </div>
                            {{-- If not from KK, show KK selector + RT selector (read-only handled by card via rt-address-selector) --}}
                        @else
                            {{-- Full mode: show RT + KK selectors --}}
                            <x-rt-address-selector :rts="$rts" :selectedRtId="$selectedRtId" :required="true" alamatName="alamat_detail" :alamatValue="old('alamat_detail', $warga->alamat_detail ?? '')" />
                            <div>
                                <label class="block text-sm font-semibold mb-1">Kartu Keluarga</label>
                                <select name="kartu_keluarga_id" id="kk_select_{{ $modalId }}" class="w-full px-3 py-2 border rounded-md text-sm">
                                    <option value="">-- Pilih KK --</option>
                                    @php
                                        $kkListAll = \App\Models\KartuKeluarga::orderBy('no_kk')->get();
                                        if (auth()->user() && !auth()->user()->isSuperAdmin() && auth()->user()->rt_id) {
                                            $kkListAll = $kkListAll->where('rt_id', auth()->user()->rt_id);
                                        }
                                    @endphp
                                    @foreach($kkListAll as $kk)
                                        <option value="{{ $kk->id }}" data-rt="{{ $kk->rt_id }}" {{ (string)$selectedKkId === (string)$kk->id ? 'selected' : '' }}>{{ $kk->no_kk }} - {{ $kk->kepala_keluarga }} ({{ $kk->rtRelation?->kode_rt ?? 'RT '.$kk->rt }}/RW {{ $kk->rw }})</option>
                                    @endforeach
                                </select>
                                @error('kartu_keluarga_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                <p class="text-xs text-gray-500 mt-1">KK difilter sesuai RT terpilih.</p>
                            </div>
                        @endif

                        @if($kartuKeluarga)
                            {{-- For KK context, show readonly RT info + alamat detail input --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-3 bg-gray-50 border rounded-md">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat Detail (Jalan)</label>
                                    <input type="text" name="alamat_detail" value="{{ old('alamat_detail', $warga->alamat_detail ?? '') }}" placeholder="Jl. / detail alamat" class="w-full px-3 py-2 border rounded-md text-sm bg-white">
                                    @error('alamat_detail')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                                    @php $rtAddr = $kartuKeluarga->rtRelation?->alamatRt; @endphp
                                    @if($rtAddr)
                                        <p class="text-xs text-gray-500 mt-1">Master RT: {{ $rtAddr->alamat }}, RW {{ $rtAddr->rw }}, {{ $rtAddr->kelurahan }}, {{ $rtAddr->kecamatan }}, {{ $rtAddr->kota }} {{ $rtAddr->kode_pos }}</p>
                                    @else
                                        <p class="text-xs text-amber-600 mt-1">Alamat RT belum dikonfigurasi.</p>
                                    @endif
                                </div>
                            </div>
                            {{-- hidden rt_id + kk already above --}}
                        @endif

                        @include('warga.partials._fields', ['warga' => $warga])

                        {{-- If from KK, alamat_detail already rendered above - avoid duplicate in _fields? _fields doesn't include alamat_detail, so ok --}}
                    </div>
                    <div class="flex justify-end gap-2 px-6 py-4 border-t bg-gray-50">
                        <button type="button" @click="open=false" class="px-4 py-2 border rounded-md text-sm">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-sp-primary text-white rounded-md text-sm font-semibold">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
    @if(!$kartuKeluarga)
    <script>
        document.addEventListener('rt-changed', e => {
            const rtId = e.detail?.rtId;
            const sel = document.getElementById('kk_select_{{ $modalId }}');
            if(!sel) return;
            Array.from(sel.options).forEach(opt=>{
                if(!opt.value) return;
                const optRt = opt.getAttribute('data-rt');
                if(!rtId) { opt.hidden=false; opt.disabled=false; }
                else if(optRt && String(optRt) !== String(rtId)) { opt.hidden=true; opt.disabled=true; if(opt.selected) sel.value=''; }
                else { opt.hidden=false; opt.disabled=false; }
            });
        });
    </script>
    @endif
</div>
