@extends('layouts.app')
@section('title', 'Detail Mutasi')
@section('content')
<div class="max-w-3xl mx-auto">
    <x-card>
        <x-slot name="title">Mutasi {{ $mutasiWarga->label_jenis }} — {{ $mutasiWarga->warga->nama ?? '-' }}</x-slot>
        <x-slot name="actions"><a href="{{ route('mutasi-warga.index') }}" class="px-3 py-1.5 border rounded-md text-sm">Kembali</a> <a href="{{ route('mutasi-warga.edit', $mutasiWarga) }}" class="px-3 py-1.5 bg-sp-primary text-white rounded-md text-sm">Edit</a></x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <div><span class="text-gray-500">Warga:</span> {{ $mutasiWarga->warga->nama ?? '-' }} ({{ $mutasiWarga->warga->nik ?? '-' }})</div>
            <div><span class="text-gray-500">Jenis:</span> {{ $mutasiWarga->label_jenis }}</div>
            <div><span class="text-gray-500">Tanggal:</span> {{ $mutasiWarga->tanggal_mutasi->format('d/m/Y') }}</div>
            <div><span class="text-gray-500">KK Lama:</span> {{ $mutasiWarga->kkLama->no_kk ?? '-' }}</div>
            <div><span class="text-gray-500">KK Baru:</span> {{ $mutasiWarga->kkBaru->no_kk ?? '-' }}</div>
            <div><span class="text-gray-500">Alamat Asal:</span> {{ $mutasiWarga->alamat_asal ?? '-' }}</div>
            <div><span class="text-gray-500">Alamat Tujuan:</span> {{ $mutasiWarga->alamat_tujuan ?? '-' }}</div>
            <div><span class="text-gray-500">Alasan:</span> {{ $mutasiWarga->alasan ?? '-' }}</div>
            <div class="md:col-span-2"><span class="text-gray-500">Keterangan:</span> {{ $mutasiWarga->keterangan ?? '-' }}</div>
        </div>
    </x-card>
</div>
@endsection
