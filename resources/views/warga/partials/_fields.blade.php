@php
    $w = $warga ?? null;
    $isEdit = isset($w) && $w->exists;
    $prefixOld = function($field, $default=null) use ($w) {
        return old($field, $w?->$field ?? $default);
    };
@endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold mb-1">NIK (16 digit) *</label>
        <input type="text" name="nik" value="{{ $prefixOld('nik') }}" maxlength="16" class="w-full px-3 py-2 border rounded-md text-sm" required>
        @error('nik')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Nama Lengkap *</label>
        <input type="text" name="nama" value="{{ $prefixOld('nama') }}" class="w-full px-3 py-2 border rounded-md text-sm" required>
        @error('nama')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Jenis Kelamin *</label>
        <select name="jenis_kelamin" class="w-full px-3 py-2 border rounded-md text-sm" required>
            <option value="L" {{ $prefixOld('jenis_kelamin')=='L'?'selected':'' }}>Laki-laki</option>
            <option value="P" {{ $prefixOld('jenis_kelamin')=='P'?'selected':'' }}>Perempuan</option>
        </select>
        @error('jenis_kelamin')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Tempat Lahir</label>
        <input type="text" name="tempat_lahir" value="{{ $prefixOld('tempat_lahir') }}" class="w-full px-3 py-2 border rounded-md text-sm">
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" value="{{ $prefixOld('tanggal_lahir', $w?->tanggal_lahir?->format('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-md text-sm">
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Agama</label>
        <select name="agama" class="w-full px-3 py-2 border rounded-md text-sm">
            <option value="">-- Pilih --</option>
            @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $a)<option value="{{ $a }}" {{ $prefixOld('agama')==$a?'selected':'' }}>{{ $a }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Pendidikan</label>
        <input type="text" name="pendidikan" value="{{ $prefixOld('pendidikan') }}" placeholder="SMA, S1, dll" class="w-full px-3 py-2 border rounded-md text-sm">
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Pekerjaan</label>
        <input type="text" name="pekerjaan" value="{{ $prefixOld('pekerjaan') }}" class="w-full px-3 py-2 border rounded-md text-sm">
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Status Perkawinan</label>
        <select name="status_perkawinan" class="w-full px-3 py-2 border rounded-md text-sm">
            <option value="">-- Pilih --</option>
            <option value="Belum Kawin" {{ $prefixOld('status_perkawinan')=='Belum Kawin'?'selected':'' }}>Belum Kawin</option>
            <option value="Kawin" {{ $prefixOld('status_perkawinan')=='Kawin'?'selected':'' }}>Kawin</option>
            <option value="Cerai Hidup" {{ $prefixOld('status_perkawinan')=='Cerai Hidup'?'selected':'' }}>Cerai Hidup</option>
            <option value="Cerai Mati" {{ $prefixOld('status_perkawinan')=='Cerai Mati'?'selected':'' }}>Cerai Mati</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Hubungan Keluarga</label>
        <select name="hubungan_keluarga" class="w-full px-3 py-2 border rounded-md text-sm">
            <option value="">-- Pilih --</option>
            @foreach(['Kepala Keluarga','Istri','Anak','Menantu','Cucu','Orang Tua','Mertua','Famili Lain'] as $h)<option value="{{ $h }}" {{ $prefixOld('hubungan_keluarga')==$h?'selected':'' }}>{{ $h }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">No HP</label>
        <input type="text" name="no_hp" value="{{ $prefixOld('no_hp') }}" class="w-full px-3 py-2 border rounded-md text-sm">
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Gol. Darah</label>
        <select name="golongan_darah" class="w-full px-3 py-2 border rounded-md text-sm">
            <option value="">--</option>
            @foreach(['A','B','AB','O'] as $g)<option value="{{ $g }}" {{ $prefixOld('golongan_darah')==$g?'selected':'' }}>{{ $g }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Kewarganegaraan</label>
        <select name="kewarganegaraan" class="w-full px-3 py-2 border rounded-md text-sm">
            <option value="WNI" {{ $prefixOld('kewarganegaraan','WNI')=='WNI'?'selected':'' }}>WNI</option>
            <option value="WNA" {{ $prefixOld('kewarganegaraan')=='WNA'?'selected':'' }}>WNA</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Nama Ayah</label>
        <input type="text" name="nama_ayah" value="{{ $prefixOld('nama_ayah') }}" class="w-full px-3 py-2 border rounded-md text-sm">
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Nama Ibu</label>
        <input type="text" name="nama_ibu" value="{{ $prefixOld('nama_ibu') }}" class="w-full px-3 py-2 border rounded-md text-sm">
    </div>
    <div>
        <label class="block text-sm font-semibold mb-1">Status Warga</label>
        <select name="status_warga" class="w-full px-3 py-2 border rounded-md text-sm">
            <option value="AKTIF" {{ $prefixOld('status_warga','AKTIF')=='AKTIF'?'selected':'' }}>Aktif</option>
            <option value="PINDAH" {{ $prefixOld('status_warga')=='PINDAH'?'selected':'' }}>Pindah</option>
            <option value="MENINGGAL" {{ $prefixOld('status_warga')=='MENINGGAL'?'selected':'' }}>Meninggal</option>
        </select>
    </div>
    <div class="md:col-span-2" x-data="{ fotoPreview: '{{ $w?->foto_url ?? '' }}', hasFoto: {{ $w?->foto ? 'true':'false' }} }">
        <label class="block text-sm font-semibold mb-1">Foto Profil <span class="text-gray-400 font-normal">(opsional, jpg/png/webp max 2MB)</span></label>
        <div class="flex items-start gap-4">
            <div class="w-20 h-20 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                <template x-if="fotoPreview">
                    <img :src="fotoPreview" class="w-full h-full object-cover">
                </template>
                <template x-if="!fotoPreview">
                    <span class="text-slate-500 font-bold text-lg" x-text="('{{ $w?->inisial ?? 'W' }}')"></span>
                </template>
            </div>
            <div class="flex-1">
                <input type="file" name="foto" accept="image/*" class="w-full text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200"
                    @change="if($event.target.files[0]){ fotoPreview = URL.createObjectURL($event.target.files[0]); hasFoto=true }">
                @error('foto')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                @if($isEdit && $w?->foto)
                    <label class="flex items-center gap-1.5 mt-2 text-xs text-red-600 cursor-pointer">
                        <input type="checkbox" name="remove_foto" value="1" @change="if($event.target.checked){ fotoPreview=''; hasFoto=false }"> Hapus foto saat ini
                    </label>
                @endif
                <p class="text-xs text-gray-500 mt-1">Foto akan ditampilkan di detail warga (avatar). Kosongkan jika tidak perlu.</p>
            </div>
        </div>
    </div>
</div>
