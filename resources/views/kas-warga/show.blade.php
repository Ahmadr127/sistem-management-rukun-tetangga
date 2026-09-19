@extends('layouts.app')
@section('title', 'Detail Kas')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Detail Kas Warga" accent="green">
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Warga</span><span class="font-semibold">{{ $kasWarga->warga->nama }} ({{ $kasWarga->warga->nik }})</span></div>
            <div class="flex justify-between"><span class="text-gray-500">RT</span><span class="px-2 py-0.5 bg-teal-100 text-teal-800 rounded-full text-xs">{{ $kasWarga->rt->kode_rt }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Periode</span><span>{{ $kasWarga->periode }} ({{ $kasWarga->periode_type }})</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Nominal</span><span class="font-bold">Rp {{ number_format($kasWarga->nominal,0,',','.') }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Status</span>@if($kasWarga->status=='sudah_bayar')<span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs">Lunas</span>@else<span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-xs">Belum</span>@endif</div>
            <div class="flex justify-between"><span class="text-gray-500">Tanggal Bayar</span><span>{{ $kasWarga->tanggal_bayar?->format('d/m/Y') ?? '-' }}</span></div>
            <div><span class="text-gray-500">Catatan</span><p class="mt-1 p-2 bg-gray-50 rounded">{{ $kasWarga->catatan ?? '-' }}</p></div>
        </div>
        <div class="flex gap-2 mt-4">
            <a href="{{ route('kas-warga.edit', $kasWarga) }}" class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-md">Edit</a>
            <a href="{{ route('kas-warga.index') }}" class="px-3 py-1.5 text-sm bg-gray-200 rounded-md">Kembali</a>
        </div>
    </x-card>
</div>
@endsection
