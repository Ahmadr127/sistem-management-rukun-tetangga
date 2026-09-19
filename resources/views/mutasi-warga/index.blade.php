@extends('layouts.app')
@section('title', 'Mutasi Warga')
@section('content')
<div class="w-full mx-auto">
    <x-card padding="false" accent="purple">
        <x-slot name="title">Mutasi Warga</x-slot>
        <x-slot name="subtitle">Masuk / Keluar / Kelahiran / Kematian / Pindah KK</x-slot>
        <x-slot name="actions"><a href="{{ route('mutasi-warga.create') }}" class="px-3 py-1.5 bg-sp-primary text-white rounded-md text-sm font-semibold"><i class="bi bi-plus-lg"></i> Catat Mutasi</a></x-slot>
        <div class="px-4 py-3 border-b bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]"><label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label><input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIK..." class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="w-40"><label class="block text-xs font-semibold text-gray-600 mb-1">Jenis</label><select name="jenis_mutasi" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"><option value="">Semua</option>@foreach(['LAHIR','MASUK','KELUAR','PINDAH_KK','MENINGGAL'] as $j)<option value="{{ $j }}" {{ request('jenis_mutasi')==$j?'selected':'' }}>{{ $j }}</option>@endforeach</select></div>
                <div class="w-36"><label class="block text-xs font-semibold text-gray-600 mb-1">Dari</label><input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="w-36"><label class="block text-xs font-semibold text-gray-600 mb-1">Sampai</label><input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-1.5 text-sm border rounded-md bg-white"></div>
                <div class="flex gap-2"><button type="submit" class="px-4 py-1.5 text-sm bg-sp-primary text-white rounded-md">Filter</button><a href="{{ route('mutasi-warga.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a></div>
            </form>
        </div>
        <x-table :columns="['No','Tanggal','Warga','Jenis','KK Lama','KK Baru','Keterangan','Aksi']" :pagination="$mutasi" accent="purple">
            @foreach($mutasi as $m)
            <tr class="hover:bg-purple-50/40">
                <td class="px-3 py-2 text-sm">{{ ($mutasi->currentPage()-1)*$mutasi->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2 text-sm">{{ $m->tanggal_mutasi->format('d/m/Y') }}</td>
                <td class="px-3 py-2"><div class="text-sm font-medium">{{ $m->warga->nama ?? '-' }}</div><div class="text-xs text-gray-500 font-mono">{{ $m->warga->nik ?? '-' }}</div></td>
                <td class="px-3 py-2">
                    @if($m->jenis_mutasi=='LAHIR')<span class="px-2 py-0.5 text-xs bg-pink-100 text-pink-800 rounded-full">Lahir</span>
                    @elseif($m->jenis_mutasi=='MASUK')<span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">Masuk</span>
                    @elseif($m->jenis_mutasi=='KELUAR')<span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded-full">Keluar</span>
                    @elseif($m->jenis_mutasi=='PINDAH_KK')<span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full">Pindah KK</span>
                    @else<span class="px-2 py-0.5 text-xs bg-gray-200 text-gray-700 rounded-full">Meninggal</span>@endif
                </td>
                <td class="px-3 py-2 text-xs font-mono">{{ $m->kkLama->no_kk ?? '-' }}</td>
                <td class="px-3 py-2 text-xs font-mono">{{ $m->kkBaru->no_kk ?? '-' }}</td>
                <td class="px-3 py-2 text-sm max-w-[180px] truncate">{{ $m->keterangan ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <x-actions>
                        <x-actions-item href="{{ route('mutasi-warga.show', $m) }}" icon="bi-eye" label="Lihat" />
                        <x-actions-item href="{{ route('mutasi-warga.edit', $m) }}" icon="bi-pencil" label="Edit" />
                        <x-actions-form action="{{ route('mutasi-warga.destroy', $m) }}" method="DELETE" icon="bi-trash" label="Hapus" confirm="Hapus mutasi ini?" />
                    </x-actions>
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>
</div>
@endsection
