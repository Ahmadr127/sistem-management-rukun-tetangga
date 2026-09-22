@extends('layouts.app')
@section('title', 'Kas Warga')
@section('content')
<div class="w-full mx-auto space-y-4">
    <x-card padding="false" accent="green">
        <x-slot name="title">Kas Warga @if(auth()->user()->rt) {{ auth()->user()->rt->kode_rt }} @endif</x-slot>
        <x-slot name="subtitle">Kelola jenis kas (bulanan / tahunan / mingguan) per KK atau perorangan — klik Buka untuk input pembayaran harian</x-slot>
        <x-slot name="actions">
            <div class="flex gap-2">
                @if(auth()->user()->hasPermission('manage_kas'))
                <a href="{{ route('kas-warga.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white rounded-md bg-green-600 hover:bg-green-700">
                    <i class="bi bi-plus-lg"></i> Tambah Jenis Kas
                </a>
                @endif
            </div>
        </x-slot>
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Jenis Kas</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama kas..." class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                </div>
                @if(auth()->user()->isSuperAdmin())
                <div class="w-32">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">RT</label>
                    <select name="rt_id" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        @foreach($rts as $rt)<option value="{{ $rt->id }}" {{ (string)request('rt_id')===(string)$rt->id?'selected':'' }}>{{ $rt->kode_rt }}</option>@endforeach
                    </select>
                </div>
                @endif
                <div class="w-32">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Periode</label>
                    <select name="periode_type" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        <option value="weekly" {{ request('periode_type')=='weekly'?'selected':'' }}>Mingguan</option>
                        <option value="monthly" {{ request('periode_type')=='monthly'?'selected':'' }}>Bulanan</option>
                        <option value="yearly" {{ request('periode_type')=='yearly'?'selected':'' }}>Tahunan</option>
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Target</label>
                    <select name="target_type" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        <option value="kk" {{ request('target_type')=='kk'?'selected':'' }}>KK</option>
                        <option value="perorangan" {{ request('target_type')=='perorangan'?'selected':'' }}>Perorangan</option>
                    </select>
                </div>
                <div class="w-28">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select name="is_active" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        <option value="1" {{ request('is_active')==='1'?'selected':'' }}>Aktif</option>
                        <option value="0" {{ request('is_active')==='0'?'selected':'' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-1.5 text-sm bg-green-600 text-white rounded-md">Filter</button>
                    <a href="{{ route('kas-warga.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a>
                </div>
            </form>
        </div>
        <x-table :columns="['No','Jenis Kas','RT','Periode','Nominal','Target','Terkumpul','Status','Aksi']" :pagination="$jenis" accent="green">
            @forelse($jenis as $j)
            <tr class="hover:bg-green-50/40">
                <td class="px-3 py-2 text-sm">{{ ($jenis->currentPage()-1)*$jenis->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2">
                    <a href="{{ route('kas-warga.show', $j) }}" class="text-sm font-semibold text-green-700 hover:underline">{{ $j->nama }}</a>
                    @if($j->deskripsi)<div class="text-xs text-gray-500 truncate max-w-[220px]">{{ $j->deskripsi }}</div>@endif
                </td>
                <td class="px-3 py-2 text-sm"><span class="px-2 py-0.5 text-xs bg-teal-100 text-teal-800 rounded-full">{{ $j->rt->kode_rt ?? '-' }}</span></td>
                <td class="px-3 py-2">
                    <span class="text-xs px-1.5 py-0.5 rounded inline-block @if($j->periode_type=='monthly') bg-blue-100 text-blue-800 @elseif($j->periode_type=='yearly') bg-orange-100 text-orange-800 @else bg-purple-100 text-purple-800 @endif">{{ $j->periode_label }}</span>
                </td>
                <td class="px-3 py-2 text-sm font-semibold">Rp {{ number_format($j->nominal,0,',','.') }}</td>
                <td class="px-3 py-2">
                    <span class="text-xs px-1.5 py-0.5 rounded inline-block @if($j->target_type=='kk') bg-amber-100 text-amber-800 @else bg-slate-200 text-slate-700 @endif">
                        <i class="bi {{ $j->target_type=='kk' ? 'bi-people' : 'bi-person' }}"></i> {{ $j->target_label }}
                    </span>
                </td>
                <td class="px-3 py-2 text-sm">
                    <div class="font-semibold text-green-700">Rp {{ number_format($j->total_terkumpul ?? 0,0,',','.') }}</div>
                    <div class="text-xs text-gray-500">{{ $j->pembayaran_count }} pembayaran</div>
                </td>
                <td class="px-3 py-2">
                    @if($j->is_active)<span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">Aktif</span>
                    @else<span class="px-2 py-0.5 text-xs bg-gray-200 text-gray-600 rounded-full">Nonaktif</span>@endif
                </td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <x-actions>
                        <x-actions-item href="{{ route('kas-warga.show', $j) }}" icon="bi-table" label="Buka Tabel" />
                        @if(auth()->user()->hasPermission('manage_kas'))
                        <x-actions-item href="{{ route('kas-warga.edit', $j) }}" icon="bi-pencil" label="Edit" />
                        <x-actions-form action="{{ route('kas-warga.destroy', $j) }}" method="DELETE" icon="bi-trash" label="Hapus" confirm="Yakin hapus jenis kas ini beserta SEMUA data pembayarannya?" />
                        @endif
                    </x-actions>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="px-3 py-8 text-center text-sm text-gray-500">Belum ada jenis kas. Klik <b>Tambah Jenis Kas</b> untuk membuat (mis. Kas Bulanan Rp20.000 / KK).</td></tr>
            @endforelse
        </x-table>
    </x-card>
</div>
@endsection
