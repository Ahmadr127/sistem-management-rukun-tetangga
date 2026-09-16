@extends('layouts.app')
@section('title', 'Detail KK')
@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <x-card>
        <x-slot name="title">KK {{ $kartuKeluarga->no_kk }}</x-slot>
        <x-slot name="subtitle">{{ $kartuKeluarga->kepala_keluarga ?? '-' }} — RT {{ $kartuKeluarga->rt ?? '-' }}/RW {{ $kartuKeluarga->rw ?? '-' }}</x-slot>
        <x-slot name="actions"><a href="{{ route('kartu-keluarga.index') }}" class="px-3 py-1.5 border rounded-md text-sm">Kembali</a> <a href="{{ route('kartu-keluarga.edit', $kartuKeluarga) }}" class="px-3 py-1.5 bg-sp-primary text-white rounded-md text-sm">Edit</a></x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <div><span class="text-gray-500">No KK:</span> <span class="font-mono font-semibold">{{ $kartuKeluarga->no_kk }}</span></div>
            <div><span class="text-gray-500">Kepala:</span> {{ $kartuKeluarga->kepala_keluarga ?? '-' }}</div>
            <div class="md:col-span-2"><span class="text-gray-500">Alamat:</span> {{ $kartuKeluarga->alamat_lengkap }}</div>
            <div><span class="text-gray-500">Dusun:</span> {{ $kartuKeluarga->dusun ?? '-' }}</div>
            <div><span class="text-gray-500">Desa:</span> {{ $kartuKeluarga->desa ?? '-' }}</div>
            <div><span class="text-gray-500">Kecamatan:</span> {{ $kartuKeluarga->kecamatan ?? '-' }}</div>
            <div><span class="text-gray-500">Kabupaten:</span> {{ $kartuKeluarga->kabupaten ?? '-' }}</div>
            <div><span class="text-gray-500">Provinsi:</span> {{ $kartuKeluarga->provinsi ?? '-' }}</div>
            <div><span class="text-gray-500">Kode Pos:</span> {{ $kartuKeluarga->kode_pos ?? '-' }}</div>
        </div>
    </x-card>
    <x-card title="Anggota Keluarga ({{ $kartuKeluarga->warga->count() }})" padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr><th class="px-3 py-2 text-left">NIK</th><th class="px-3 py-2 text-left">Nama</th><th class="px-3 py-2 text-left">L/P</th><th class="px-3 py-2 text-left">Hubungan</th><th class="px-3 py-2 text-left">Pekerjaan</th><th class="px-3 py-2 text-left">Status</th></tr></thead>
                <tbody class="divide-y">
                    @forelse($kartuKeluarga->warga as $w)<tr><td class="px-3 py-2 font-mono">{{ $w->nik }}</td><td class="px-3 py-2 font-medium">{{ $w->nama }}</td><td class="px-3 py-2">{{ $w->jenis_kelamin }}</td><td class="px-3 py-2">{{ $w->hubungan_keluarga ?? '-' }}</td><td class="px-3 py-2">{{ $w->pekerjaan ?? '-' }}</td><td class="px-3 py-2">{{ $w->status_warga }}</td></tr>@empty<tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Belum ada anggota</td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
