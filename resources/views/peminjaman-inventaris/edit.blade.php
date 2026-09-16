@extends('layouts.app')
@section('title', 'Edit Peminjaman')
@section('content')
<div class="max-w-3xl mx-auto">
    <x-card>
        <x-slot name="title">Edit Peminjaman</x-slot>
        <x-slot name="actions"><a href="{{ route('peminjaman-inventaris.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        <form action="{{ route('peminjaman-inventaris.update', $peminjaman) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold mb-1">Barang *</label><select name="inventaris_id" class="w-full px-3 py-2 border rounded-md text-sm" required>@foreach($inventaris as $inv)<option value="{{ $inv->id }}" {{ old('inventaris_id',$peminjaman->inventaris_id)==$inv->id?'selected':'' }}>{{ $inv->kode_barang }} - {{ $inv->nama_barang }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">Jumlah *</label><input type="number" name="jumlah_pinjam" value="{{ old('jumlah_pinjam',$peminjaman->jumlah_pinjam) }}" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Warga</label><select name="warga_id" class="w-full px-3 py-2 border rounded-md text-sm"><option value="">-- Non-warga --</option>@foreach($warga as $w)<option value="{{ $w->id }}" {{ old('warga_id',$peminjaman->warga_id)==$w->id?'selected':'' }}>{{ $w->nama }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-semibold mb-1">Nama Peminjam</label><input type="text" name="nama_peminjam" value="{{ old('nama_peminjam',$peminjaman->nama_peminjam) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">No HP</label><input type="text" name="no_hp_peminjam" value="{{ old('no_hp_peminjam',$peminjaman->no_hp_peminjam) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Tgl Pinjam *</label><input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam',$peminjaman->tanggal_pinjam->format('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-md text-sm" required></div>
                <div><label class="block text-sm font-semibold mb-1">Rencana Kembali</label><input type="date" name="tanggal_kembali_rencana" value="{{ old('tanggal_kembali_rencana',$peminjaman->tanggal_kembali_rencana?->format('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Aktual Kembali</label><input type="date" name="tanggal_kembali_aktual" value="{{ old('tanggal_kembali_aktual',$peminjaman->tanggal_kembali_aktual?->format('Y-m-d')) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
                <div><label class="block text-sm font-semibold mb-1">Status *</label><select name="status" class="w-full px-3 py-2 border rounded-md text-sm">@foreach(['DIPINJAM','DIKEMBALIKAN','TERLAMBAT','HILANG','RUSAK'] as $s)<option value="{{ $s }}" {{ old('status',$peminjaman->status)==$s?'selected':'' }}>{{ $s }}</option>@endforeach</select></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Keperluan</label><textarea name="keperluan" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('keperluan',$peminjaman->keperluan) }}</textarea></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Keterangan</label><textarea name="keterangan" rows="2" class="w-full px-3 py-2 border rounded-md text-sm">{{ old('keterangan',$peminjaman->keterangan) }}</textarea></div>
                <div class="md:col-span-2"><label class="block text-sm font-semibold mb-1">Kondisi Kembali</label><input type="text" name="kondisi_kembali" value="{{ old('kondisi_kembali',$peminjaman->kondisi_kembali) }}" class="w-full px-3 py-2 border rounded-md text-sm"></div>
            </div>
            <div class="flex justify-end gap-2 pt-4"><a href="{{ route('peminjaman-inventaris.index') }}" class="px-4 py-2 border rounded-md text-sm">Batal</a><button type="submit" class="px-6 py-2 bg-sp-primary text-white rounded-md text-sm font-semibold">Update</button></div>
        </form>
    </x-card>
</div>
@endsection
