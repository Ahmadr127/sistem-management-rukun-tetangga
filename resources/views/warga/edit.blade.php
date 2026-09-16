@extends('layouts.app')
@section('title', 'Edit Warga')
@section('content')
<div class="max-w-4xl mx-auto">
    <x-card>
        <x-slot name="title">Edit Warga: {{ $warga->nama }}</x-slot>
        <x-slot name="actions"><a href="{{ route('warga.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        <form action="{{ route('warga.update', $warga) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Kartu Keluarga</label>
                    <select name="kartu_keluarga_id" class="w-full px-3 py-2 border rounded-md text-sm">
                        <option value="">-- Tidak ada --</option>
                        @foreach($kkList as $kk)<option value="{{ $kk->id }}" {{ old('kartu_keluarga_id',$warga->kartu_keluarga_id)==$kk->id?'selected':'' }}>{{ $kk->no_kk }} - {{ $kk->kepala_keluarga }}</option>@endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-semibold mb-1">NIK *</label><input type="text" name="nik" value="{{ old('nik',$warga->nik) }}" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Nama *</label><input type="text" name="nama" value="{{ old('nama',$warga->nama) }}" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Jenis Kelamin *</label><select name="jenis_kelamin" class="w-full px-3 py-2 border rounded-md text-sm"><option value="L" {{ old('jenis_kelamin',$warga->jenis_kelamin)=='L'?'selected':'' }}>Laki-laki</option><option value="P" {{ old('jenis_kelamin',$warga->jenis_kelamin)=='P'?'selected':'' }}>Perempuan</option></select></div>
                <div><label class="block text-sm font-semibold mb-1">Tempat Lahir</label><input type="text" name="tempat_lahir" value="{{ old('tempat_lahir',$warga->tempat_lahir) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Tanggal Lahir</label><input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir',$warga->tanggal_lahir?->format('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Agama</label><select name="agama" class="w-full px-3 py-2 border rounded-md text-sm"><option value="">--</option>@foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $a)<option value="{{ $a }}" {{ old('agama',$warga->agama)==$a?'selected':'' }}>{{ $a }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">Pendidikan</label><input type="text" name="pendidikan" value="{{ old('pendidikan',$warga->pendidikan) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Pekerjaan</label><input type="text" name="pekerjaan" value="{{ old('pekerjaan',$warga->pekerjaan) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Status Perkawinan</label><select name="status_perkawinan" class="w-full px-3 py-2 border rounded-md text-sm"><option value="">--</option><option value="Belum Kawin" {{ old('status_perkawinan',$warga->status_perkawinan)=='Belum Kawin'?'selected':'' }}>Belum Kawin</option><option value="Kawin" {{ old('status_perkawinan',$warga->status_perkawinan)=='Kawin'?'selected':'' }}>Kawin</option><option value="Cerai Hidup" {{ old('status_perkawinan',$warga->status_perkawinan)=='Cerai Hidup'?'selected':'' }}>Cerai Hidup</option><option value="Cerai Mati" {{ old('status_perkawinan',$warga->status_perkawinan)=='Cerai Mati'?'selected':'' }}>Cerai Mati</option></select></div>
                <div><label class="block text-sm font-semibold mb-1">Hubungan Keluarga</label><select name="hubungan_keluarga" class="w-full px-3 py-2 border rounded-md text-sm"><option value="">--</option>@foreach(['Kepala Keluarga','Istri','Anak','Menantu','Cucu','Orang Tua','Mertua','Famili Lain'] as $h)<option value="{{ $h }}" {{ old('hubungan_keluarga',$warga->hubungan_keluarga)==$h?'selected':'' }}>{{ $h }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">No HP</label><input type="text" name="no_hp" value="{{ old('no_hp',$warga->no_hp) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Gol. Darah</label><select name="golongan_darah" class="w-full px-3 py-2 border rounded-md text-sm"><option value="">--</option>@foreach(['A','B','AB','O'] as $g)<option value="{{ $g }}" {{ old('golongan_darah',$warga->golongan_darah)==$g?'selected':'' }}>{{ $g }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">Kewarganegaraan</label><select name="kewarganegaraan" class="w-full px-3 py-2 border rounded-md text-sm"><option value="WNI" {{ old('kewarganegaraan',$warga->kewarganegaraan)=='WNI'?'selected':'' }}>WNI</option><option value="WNA" {{ old('kewarganegaraan',$warga->kewarganegaraan)=='WNA'?'selected':'' }}>WNA</option></select></div>
                <div><label class="block text-sm font-semibold mb-1">Nama Ayah</label><input type="text" name="nama_ayah" value="{{ old('nama_ayah',$warga->nama_ayah) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Nama Ibu</label><input type="text" name="nama_ibu" value="{{ old('nama_ibu',$warga->nama_ibu) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Status Warga</label><select name="status_warga" class="w-full px-3 py-2 border rounded-md text-sm"><option value="AKTIF" {{ old('status_warga',$warga->status_warga)=='AKTIF'?'selected':'' }}>Aktif</option><option value="PINDAH" {{ old('status_warga',$warga->status_warga)=='PINDAH'?'selected':'' }}>Pindah</option><option value="MENINGGAL" {{ old('status_warga',$warga->status_warga)=='MENINGGAL'?'selected':'' }}>Meninggal</option></select></div>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <a href="{{ route('warga.index') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a>
                <button type="submit" class="px-6 py-2 bg-sp-primary text-white rounded-md text-sm font-semibold">Update</button>
            </div>
        </form>
    </x-card>
</div>
@endsection
