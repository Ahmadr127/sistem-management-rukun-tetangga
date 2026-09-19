@props([
    'rts' => collect(),
    'selectedRtId' => null,
    'required' => false,
    'alamatName' => null,
    'alamatValue' => null,
    'editableAlamat' => true,
])

@php
    $user = auth()->user();
    $isSuper = $user?->isSuperAdmin();
    $effectiveRtId = $selectedRtId ?? ($isSuper ? old('rt_id') : $user?->rt_id);
    $initialAlamat = null;
    $initialArray = null;
    if ($effectiveRtId) {
        $rtModel = $rts->firstWhere('id', (int)$effectiveRtId) ?? \App\Models\Rt::find($effectiveRtId);
        $initialAlamat = $rtModel?->alamatRt;
        $initialArray = $initialAlamat ? $initialAlamat->toApiArray() : ['rt_id'=>$effectiveRtId,'rt'=>$rtModel?->kode_rt,'rw'=>'','alamat'=>'','kelurahan'=>'','kecamatan'=>'','kota'=>'','provinsi'=>'','kode_pos'=>''];
        // jika ada alamatValue (old atau edit), pakai sebagai alamat jalan bebas (tidak lock dari master)
        if ($alamatValue !== null && $alamatValue !== '') {
            $initialArray['alamat'] = $alamatValue;
        } elseif ($alamatName && old($alamatName) !== null) {
            $initialArray['alamat'] = old($alamatName);
        }
    } elseif ($alamatValue !== null) {
        $initialArray = ['alamat'=>$alamatValue,'rw'=>'','kelurahan'=>'','kecamatan'=>'','kota'=>'','provinsi'=>'','kode_pos'=>''];
    }
@endphp

<div class="space-y-3"     x-data="rtAddressSelector({{ Js::from(['initialRtId' => $effectiveRtId, 'isSuper'=>$isSuper, 'initialAlamat' => $initialArray]) }})" x-init="init()">
    @if($isSuper)
        <div>
            <label class="block text-sm font-semibold mb-1">RT @if($required)<span class="text-red-500">*</span>@endif</label>
            <select name="rt_id" x-model="rtId" @change="fetchAlamat()" class="w-full px-3 py-2 border rounded-md text-sm bg-white" {{ $required?'required':'' }}>
                <option value="">-- Pilih RT --</option>
                @foreach($rts as $rt)
                    <option value="{{ $rt->id }}">{{ $rt->kode_rt }} - {{ $rt->nama_rt }}</option>
                @endforeach
            </select>
            @error('rt_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
    @else
        <div>
            <label class="block text-sm font-semibold mb-1">RT</label>
            <div class="px-3 py-2 bg-teal-50 border border-teal-200 rounded-md text-sm font-semibold text-teal-800 flex items-center gap-2">
                <i class="bi bi-geo-alt-fill"></i> {{ $user->rt?->kode_rt ?? '-' }} - {{ $user->rt?->nama_rt ?? '-' }}
            </div>
            <input type="hidden" name="rt_id" x-model="rtId" value="{{ $user->rt_id }}">
        </div>
    @endif

    <div x-show="warning" x-cloak class="p-3 bg-amber-50 border border-amber-200 rounded-md text-xs text-amber-800 flex items-start gap-2">
        <i class="bi bi-exclamation-triangle mt-0.5"></i>
        <div>
            <div class="font-semibold">Alamat RT belum dikonfigurasi.</div>
            <template x-if="$root.__isSuper">
                <a href="{{ route('alamat-rt.create') }}" class="underline font-semibold">Konfigurasi Alamat</a>
            </template>
            <template x-if="!$root.__isSuper">
                <span>Tunggu administrator mengatur alamat RT.</span>
            </template>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-3 bg-gray-50 border border-gray-200 rounded-md">
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat (Jalan) <span class="text-gray-400 font-normal">(bebas edit)</span></label>
            @if($alamatName)
                <input type="text" name="{{ $alamatName }}" x-model="alamat.alamat" placeholder="Jl. Raya Bogor / detail jalan (bebas edit)" class="w-full px-3 py-2 border rounded-md text-sm bg-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                @error($alamatName)<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            @else
                <input type="text" x-model="alamat.alamat" placeholder="Jl. Raya Bogor (bebas edit)" class="w-full px-3 py-2 border rounded-md text-sm bg-white focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                <p class="text-xs text-gray-400 mt-1">Bebas edit — tidak terkunci dari master.</p>
            @endif
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">RW</label>
            <input type="text" :value="alamat.rw ?? ''" readonly class="w-full px-3 py-2 border rounded-md text-sm bg-gray-50">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Kelurahan/Desa</label>
            <input type="text" :value="alamat.kelurahan ?? ''" readonly class="w-full px-3 py-2 border rounded-md text-sm bg-gray-50">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Kecamatan</label>
            <input type="text" :value="alamat.kecamatan ?? ''" readonly class="w-full px-3 py-2 border rounded-md text-sm bg-gray-50">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Kota/Kabupaten</label>
            <input type="text" :value="alamat.kota ?? ''" readonly class="w-full px-3 py-2 border rounded-md text-sm bg-gray-50">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Provinsi</label>
            <input type="text" :value="alamat.provinsi ?? ''" readonly class="w-full px-3 py-2 border rounded-md text-sm bg-gray-50">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Kode Pos</label>
            <input type="text" :value="alamat.kode_pos ?? ''" readonly class="w-full px-3 py-2 border rounded-md text-sm bg-gray-50">
        </div>
        <div class="text-xs text-gray-500 md:col-span-2">
            <span class="font-semibold">Info:</span> RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos readonly dari master RT. Alamat jalan bebas edit.
        </div>
    </div>

    <template x-if="false"><span></span></template>
</div>

<script>
function rtAddressSelector({initialRtId, isSuper, initialAlamat}) {
    return {
        rtId: initialRtId ? String(initialRtId) : '',
        alamat: initialAlamat ?? {alamat:'',rw:'',kelurahan:'',kecamatan:'',kota:'',provinsi:'',kode_pos:''},
        warning: false,
        init() {
            this.$root.__isSuper = isSuper;
            // initialAlamat sudah diisi dari server (master + custom alamat jalan)
            // tidak perlu fetch ulang di init, cukup dispatch untuk filter KK
            if (!this.alamat || (!this.alamat.rw && !this.alamat.kelurahan && !this.alamat.kecamatan)) {
                // jika master belum ada, tampilkan warning jika rtId ada tapi alamat kosong
                this.warning = !!this.rtId && !this.alamat?.alamat && !this.alamat?.kelurahan;
            }
            if (this.rtId) this.$dispatch('rt-changed', {rtId: this.rtId});
        },
        async fetchAlamat() {
            if (!this.rtId) {
                this.alamat = {alamat:'',rw:'',kelurahan:'',kecamatan:'',kota:'',provinsi:'',kode_pos:''};
                this.warning = false;
                // dispatch event for dependent selects
                this.$dispatch('rt-changed', {rtId: null});
                return;
            }
            try {
                const res = await fetch(`/api/rts/${this.rtId}/alamat`, { headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' }});
                if (res.status === 404) {
                    this.warning = true;
                    this.alamat = {alamat:'',rw:'',kelurahan:'',kecamatan:'',kota:'',provinsi:'',kode_pos:''};
                } else if (!res.ok) {
                    this.warning = true;
                    this.alamat = {alamat:'',rw:'',kelurahan:'',kecamatan:'',kota:'',provinsi:'',kode_pos:''};
                } else {
                    this.warning = false;
                    this.alamat = await res.json();
                }
                this.$dispatch('rt-changed', {rtId: this.rtId});
            } catch(e) {
                this.warning = true;
            }
        }
    }
}
</script>
