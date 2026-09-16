@extends('layouts.app')
@section('title', 'Detail Warga')
@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <x-card>
        <x-slot name="title">{{ $warga->nama }} — {{ $warga->nik }}</x-slot>
        <x-slot name="actions"><a href="{{ route('warga.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a> <a href="{{ route('warga.edit', $warga) }}" class="text-sm px-3 py-1.5 bg-sp-primary text-white rounded-md">Edit</a></x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">NIK:</span> <span class="font-mono font-semibold">{{ $warga->nik }}</span></div>
            <div><span class="text-gray-500">KK:</span> {{ $warga->kartuKeluarga->no_kk ?? '-' }} ({{ $warga->kartuKeluarga->kepala_keluarga ?? '-' }})</div>
            <div><span class="text-gray-500">TTL:</span> {{ $warga->tempat_lahir }}, {{ $warga->tanggal_lahir?->format('d/m/Y') ?? '-' }} ({{ $warga->umur ? $warga->umur.' th' : '-' }})</div>
            <div><span class="text-gray-500">L/P:</span> {{ $warga->jenis_kelamin=='L'?'Laki-laki':'Perempuan' }}</div>
            <div><span class="text-gray-500">Agama:</span> {{ $warga->agama ?? '-' }}</div>
            <div><span class="text-gray-500">Pendidikan:</span> {{ $warga->pendidikan ?? '-' }}</div>
            <div><span class="text-gray-500">Pekerjaan:</span> {{ $warga->pekerjaan ?? '-' }}</div>
            <div><span class="text-gray-500">Status Kawin:</span> {{ $warga->status_perkawinan ?? '-' }}</div>
            <div><span class="text-gray-500">Hub. Keluarga:</span> {{ $warga->hubungan_keluarga ?? '-' }}</div>
            <div><span class="text-gray-500">No HP:</span> {{ $warga->no_hp ?? '-' }}</div>
            <div><span class="text-gray-500">Alamat KK:</span> {{ $warga->kartuKeluarga->alamat_lengkap ?? '-' }}</div>
            <div><span class="text-gray-500">Status Warga:</span> <span class="px-2 py-0.5 rounded-full text-xs {{ $warga->status_warga=='AKTIF'?'bg-green-100 text-green-800':'bg-gray-200' }}">{{ $warga->status_warga }}</span></div>
        </div>
    </x-card>
    @if($warga->mutasi->count())
    <x-card title="Riwayat Mutasi" padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr><th class="px-3 py-2 text-left">Tanggal</th><th class="px-3 py-2 text-left">Jenis</th><th class="px-3 py-2 text-left">KK Lama</th><th class="px-3 py-2 text-left">KK Baru</th><th class="px-3 py-2 text-left">Keterangan</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($warga->mutasi as $m)<tr><td class="px-3 py-2">{{ $m->tanggal_mutasi->format('d/m/Y') }}</td><td class="px-3 py-2">{{ $m->label_jenis }}</td><td class="px-3 py-2">{{ $m->kkLama->no_kk ?? '-' }}</td><td class="px-3 py-2">{{ $m->kkBaru->no_kk ?? '-' }}</td><td class="px-3 py-2">{{ $m->keterangan ?? '-' }}</td></tr>@endforeach
                </tbody>
            </table>
        </div>
    </x-card>
    @endif
</div>
@endsection
