@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('content')
<div class="space-y-4">
    <x-card>
        <x-slot name="title">Laporan Keuangan</x-slot>
        <x-slot name="subtitle">Rekap pemasukan, pengeluaran, dan saldo</x-slot>
        <form method="GET" class="flex flex-wrap gap-3 items-end mt-3">
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Dari</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-1.5 text-sm border rounded-md bg-white"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">Sampai</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-1.5 text-sm border rounded-md bg-white"></div>
            <button type="submit" class="px-4 py-1.5 text-sm bg-sp-primary text-white rounded-md">Tampilkan</button>
            <a href="{{ route('keuangan.laporan') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a>
            <a href="javascript:window.print()" class="px-4 py-1.5 text-sm border rounded-md"><i class="bi bi-printer"></i> Cetak</a>
        </form>
    </x-card>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Total Pemasukan</div><div class="text-xl font-bold text-green-600">Rp {{ number_format($laporan['total_pemasukan'],0,',','.') }}</div></div>
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Total Pengeluaran</div><div class="text-xl font-bold text-red-600">Rp {{ number_format($laporan['total_pengeluaran'],0,',','.') }}</div></div>
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Saldo</div><div class="text-xl font-bold {{ $laporan['saldo']>=0?'text-sp-primary':'text-red-600' }}">Rp {{ number_format($laporan['saldo'],0,',','.') }}</div></div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-card title="Pemasukan per Kategori"><div class="space-y-2">@forelse($laporan['by_kategori_pemasukan'] as $r)<div class="flex justify-between text-sm border-b py-1"><span>{{ $r->kategori }}</span><span class="font-semibold text-green-600">Rp {{ number_format($r->total,0,',','.') }}</span></div>@empty<p class="text-sm text-gray-500">Tidak ada data</p>@endforelse</div></x-card>
        <x-card title="Pengeluaran per Kategori"><div class="space-y-2">@forelse($laporan['by_kategori_pengeluaran'] as $r)<div class="flex justify-between text-sm border-b py-1"><span>{{ $r->kategori }}</span><span class="font-semibold text-red-600">Rp {{ number_format($r->total,0,',','.') }}</span></div>@empty<p class="text-sm text-gray-500">Tidak ada data</p>@endforelse</div></x-card>
    </div>
    <x-card title="Tren 6 Bulan Terakhir">
        <x-chart type="bar" :labels="collect($laporan['monthly'])->pluck('label')->toArray()" :datasets="[[ 'label'=>'Pemasukan','data'=>collect($laporan['monthly'])->pluck('pemasukan')->toArray(),'backgroundColor'=>'#16a34a' ],[ 'label'=>'Pengeluaran','data'=>collect($laporan['monthly'])->pluck('pengeluaran')->toArray(),'backgroundColor'=>'#dc2626' ]]" :height="260" />
    </x-card>
    <x-card title="Detail Transaksi" padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr><th class="px-3 py-2 text-left">Tanggal</th><th class="px-3 py-2 text-left">Jenis</th><th class="px-3 py-2 text-left">Kategori</th><th class="px-3 py-2 text-right">Jumlah</th><th class="px-3 py-2 text-left">Keterangan</th></tr></thead>
                <tbody class="divide-y">@foreach($laporan['list'] as $k)<tr><td class="px-3 py-2">{{ $k->tanggal->format('d/m/Y') }}</td><td class="px-3 py-2">@if($k->jenis=='PEMASUKAN')<span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">Masuk</span>@else<span class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">Keluar</span>@endif</td><td class="px-3 py-2">{{ $k->kategori }}</td><td class="px-3 py-2 text-right font-semibold {{ $k->jenis=='PEMASUKAN'?'text-green-600':'text-red-600' }}">Rp {{ number_format($k->jumlah,0,',','.') }}</td><td class="px-3 py-2">{{ $k->keterangan ?? '-' }}</td></tr>@endforeach</tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
