@extends('layouts.app')
@section('title', 'Edit Inventaris')
@section('content')
<div class="max-w-3xl mx-auto">
    <x-card>
        <x-slot name="title">Edit Inventaris: {{ $inventaris->nama_barang }}</x-slot>
        <x-slot name="actions"><a href="{{ route('inventaris.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        <form action="{{ route('inventaris.update', $inventaris) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold mb-1">Kode *</label><input type="text" name="kode_barang" value="{{ old('kode_barang',$inventaris->kode_barang) }}" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Nama *</label><input type="text" name="nama_barang" value="{{ old('nama_barang',$inventaris->nama_barang) }}" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Kategori</label><input type="text" name="kategori" value="{{ old('kategori',$inventaris->kategori) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Jumlah *</label><input type="number" name="jumlah" value="{{ old('jumlah',$inventaris->jumlah) }}" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Satuan</label><input type="text" name="satuan" value="{{ old('satuan',$inventaris->satuan) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Kondisi *</label><select name="kondisi" class="w-full px-3 py-2 border rounded-md text-sm">@foreach(['BAIK','RUSAK_RINGAN','RUSAK_BERAT','HILANG'] as $k)<option value="{{ $k }}" {{ old('kondisi',$inventaris->kondisi)==$k?'selected':'' }}>{{ $k }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">Lokasi</label><input type="text" name="lokasi" value="{{ old('lokasi',$inventaris->lokasi) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Harga Satuan</label><input type="number" name="harga_satuan" value="{{ old('harga_satuan',$inventaris->harga_satuan) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Sumber Dana</label><input type="text" name="sumber_dana" value="{{ old('sumber_dana',$inventaris->sumber_dana) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Tanggal Pengadaan</label><input type="date" name="tanggal_pengadaan" value="{{ old('tanggal_pengadaan',$inventaris->tanggal_pengadaan?->format('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Keterangan</label><textarea name="keterangan" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('keterangan',$inventaris->keterangan) }}</textarea></div>
                <div class="md:col-span-2" x-data="{ previews: [] }">
                    <label class="block text-sm font-semibold mb-1">Foto Barang <span class="text-gray-400 font-normal">(bisa lebih dari 1, jpg/png/webp max 3MB, klik foto untuk perbesar)</span></label>
                    @if($inventaris->fotoUrls && count($inventaris->fotoUrls))
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($inventaris->fotoUrls as $idx=>$url)
                                <div class="relative group">
                                    <img src="{{ $url }}" class="w-20 h-20 rounded border object-cover cursor-pointer hover:opacity-80" onclick="window.open('{{ $url }}','_blank')" title="Klik untuk perbesar">
                                    <label class="absolute -top-1 -right-1 bg-white border rounded-full w-5 h-5 flex items-center justify-center text-red-600 hover:bg-red-50 cursor-pointer" title="Hapus foto ini">
                                        <input type="checkbox" name="remove_foto[]" value="{{ $inventaris->fotoArray[$idx] }}" class="hidden peer"><i class="bi bi-x text-xs"></i>
                                    </label>
                                    <div class="hidden peer-checked:flex absolute inset-0 bg-red-500/60 rounded items-center justify-center text-white text-xs font-semibold">Hapus</div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mb-2">Foto saat ini (klik untuk perbesar). Centang X untuk hapus.</p>
                    @endif
                    <input type="file" name="foto[]" multiple accept="image/*" class="w-full text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100" @change="previews=[]; for(let f of $event.target.files) previews.push(URL.createObjectURL(f))">
                    @error('foto')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    @error('foto.*')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    <div class="flex flex-wrap gap-2 mt-2" x-show="previews.length">
                        <template x-for="(src,i) in previews" :key="i"><img :src="src" class="w-16 h-16 rounded border object-cover cursor-pointer" @click="window.open(src,'_blank')"></template>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Tambah foto baru (bisa pilih banyak). Foto baru akan ditambahkan ke galeri.</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-4"><a href="{{ route('inventaris.index') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a><button type="submit" class="px-6 py-2 bg-sp-primary text-white rounded-md text-sm font-semibold">Update</button></div>
        </form>
    </x-card>
</div>
@endsection
