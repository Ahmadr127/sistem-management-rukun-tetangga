@extends('layouts.app')
@section('title', 'Pengeluaran')
@section('content')
<div class="w-full mx-auto space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Total Pemasukan</div><div class="text-xl font-bold text-green-600">Rp {{ number_format($saldo['pemasukan'],0,',','.') }}</div></div>
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Total Pengeluaran</div><div class="text-xl font-bold text-red-600">Rp {{ number_format($saldo['pengeluaran'],0,',','.') }}</div></div>
        <div class="bg-white rounded-lg border p-4"><div class="text-xs text-gray-500">Saldo</div><div class="text-xl font-bold text-sp-primary">Rp {{ number_format($saldo['saldo'],0,',','.') }}</div></div>
    </div>
    <x-card padding="false" accent="red">
        <x-slot name="title">Transaksi Pengeluaran</x-slot>
        <x-slot name="actions"><a href="{{ route('keuangan.pengeluaran.create') }}" class="px-3 py-1.5 bg-red-600 text-white rounded-md text-sm font-semibold"><i class="bi bi-plus-lg"></i> Tambah Pengeluaran</a></x-slot>
        <div class="px-4 py-3 border-b bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]"><label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label><input type="text" name="search" value="{{ request('search') }}" placeholder="Kategori, keterangan..." class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="w-40"><label class="block text-xs font-semibold text-gray-600 mb-1">Kategori</label><select name="kategori" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"><option value="">Semua</option>@foreach($categories as $c)<option value="{{ $c }}" {{ request('kategori')==$c?'selected':'' }}>{{ $c }}</option>@endforeach</select></div>
                <div class="w-36"><label class="block text-xs font-semibold text-gray-600 mb-1">Dari</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="w-36"><label class="block text-xs font-semibold text-gray-600 mb-1">Sampai</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="flex gap-2"><button type="submit" class="px-4 py-1.5 text-sm bg-sp-primary text-white rounded-md">Filter</button><a href="{{ route('keuangan.pengeluaran.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a></div>
            </form>
        </div>
        <x-table :columns="['No','Tanggal','Kategori','Jumlah','Sumber Dana','Keterangan','Aksi']" :pagination="$keuangan" accent="red">
            @foreach($keuangan as $k)
            <tr class="hover:bg-red-50/40">
                <td class="px-3 py-2 text-sm">{{ ($keuangan->currentPage()-1)*$keuangan->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2 text-sm">{{ $k->tanggal->format('d/m/Y') }}</td>
                <td class="px-3 py-2"><span class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">{{ $k->kategori }}</span></td>
                <td class="px-3 py-2 text-sm font-semibold text-red-600">Rp {{ number_format($k->jumlah,0,',','.') }}</td>
                <td class="px-3 py-2 text-sm">{{ $k->sumber_dana ?? '-' }}</td>
                <td class="px-3 py-2 text-sm max-w-[200px] truncate">{{ $k->keterangan ?? $k->deskripsi ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <x-actions>
                        <x-actions-item href="{{ route('keuangan.edit', $k) }}" icon="bi-pencil" label="Edit" />
                        <x-actions-form action="{{ route('keuangan.destroy', $k) }}" method="DELETE" icon="bi-trash" label="Hapus" confirm="Hapus transaksi ini?" />
                    </x-actions>
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>
</div>
@endsection
