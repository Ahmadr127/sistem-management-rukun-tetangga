@extends('layouts.app')
@section('title', 'Kas Warga')
@section('content')
<div class="w-full mx-auto space-y-4">
    <x-card padding="false" accent="green">
        <x-slot name="title">Kas Warga @if(auth()->user()->rt) {{ auth()->user()->rt->kode_rt }} @endif</x-slot>
        <x-slot name="subtitle">Kelola kas mingguan/bulanan per warga</x-slot>
        <x-slot name="actions">
            <div class="flex gap-2">
                @if(auth()->user()->hasPermission('manage_kas'))
                <button onclick="document.getElementById('generateModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white rounded-md bg-emerald-600 hover:bg-emerald-700">
                    <i class="bi bi-lightning"></i> Generate Kas
                </button>
                <a href="{{ route('kas-warga.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-white rounded-md bg-green-600 hover:bg-green-700">
                    <i class="bi bi-plus-lg"></i> Tambah Kas
                </a>
                @endif
            </div>
        </x-slot>
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Warga</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIK..." class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
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
                <div class="w-28">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis</label>
                    <select name="periode_type" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        <option value="weekly" {{ request('periode_type')=='weekly'?'selected':'' }}>Mingguan</option>
                        <option value="monthly" {{ request('periode_type')=='monthly'?'selected':'' }}>Bulanan</option>
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Periode</label>
                    <select name="periode" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        @foreach($periodes as $p)<option value="{{ $p }}" {{ request('periode')==$p?'selected':'' }}>{{ $p }}</option>@endforeach
                    </select>
                </div>
                <div class="w-28">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-md bg-white">
                        <option value="">Semua</option>
                        <option value="belum_bayar" {{ request('status')=='belum_bayar'?'selected':'' }}>Belum</option>
                        <option value="sudah_bayar" {{ request('status')=='sudah_bayar'?'selected':'' }}>Lunas</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-1.5 text-sm bg-green-600 text-white rounded-md">Filter</button>
                    <a href="{{ route('kas-warga.index') }}" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Reset</a>
                </div>
            </form>
        </div>
        <x-table :columns="['No','Periode','Warga','RT','Nominal','Status','Tanggal Bayar','Aksi']" :pagination="$kas" accent="green">
            @foreach($kas as $k)
            <tr class="hover:bg-green-50/40">
                <td class="px-3 py-2 text-sm">{{ ($kas->currentPage()-1)*$kas->perPage() + $loop->iteration }}</td>
                <td class="px-3 py-2">
                    <div class="text-sm font-medium">{{ $k->periode }}</div>
                    <div class="text-xs px-1.5 py-0.5 rounded inline-block @if($k->periode_type=='monthly') bg-blue-100 text-blue-800 @else bg-purple-100 text-purple-800 @endif">{{ $k->periode_type=='monthly'?'Bulanan':'Mingguan' }}</div>
                </td>
                <td class="px-3 py-2">
                    <div class="text-sm font-medium">{{ $k->warga->nama }}</div>
                    <div class="text-xs text-gray-500 font-mono">{{ $k->warga->nik }}</div>
                </td>
                <td class="px-3 py-2 text-sm"><span class="px-2 py-0.5 text-xs bg-teal-100 text-teal-800 rounded-full">{{ $k->rt->kode_rt ?? '-' }}</span></td>
                <td class="px-3 py-2 text-sm font-semibold">Rp {{ number_format($k->nominal,0,',','.') }}</td>
                <td class="px-3 py-2">
                    @if($k->status=='sudah_bayar')<span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">Lunas</span>
                    @else<span class="px-2 py-0.5 text-xs bg-amber-100 text-amber-800 rounded-full">Belum</span>@endif
                </td>
                <td class="px-3 py-2 text-sm">{{ $k->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
                <td class="px-3 py-2 whitespace-nowrap">
                    <x-actions>
                        <x-actions-item href="{{ route('kas-warga.show', $k) }}" icon="bi-eye" label="Lihat" />
                        @if(auth()->user()->hasPermission('manage_kas'))
                        <x-actions-item href="{{ route('kas-warga.edit', $k) }}" icon="bi-pencil" label="Edit" />
                        <x-actions-form action="{{ route('kas-warga.destroy', $k) }}" method="DELETE" icon="bi-trash" label="Hapus" confirm="Yakin hapus kas ini?" />
                        @endif
                    </x-actions>
                </td>
            </tr>
            @endforeach
        </x-table>
    </x-card>

    <!-- Generate Modal -->
    <div id="generateModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="font-bold text-gray-900 mb-2">Generate Kas Warga</h3>
            <p class="text-xs text-gray-500 mb-4">Membuat kewajiban kas untuk seluruh warga aktif di RT terpilih. Duplicate periode akan dilewati.</p>
            <form method="POST" action="{{ route('kas-warga.generate') }}" class="space-y-3">
                @csrf
                @if(auth()->user()->isSuperAdmin())
                <div>
                    <label class="block text-sm font-semibold mb-1">RT *</label>
                    <select name="rt_id" class="w-full px-3 py-2 border rounded-md text-sm" required>
                        @foreach($rts as $rt)<option value="{{ $rt->id }}">{{ $rt->kode_rt }} - {{ $rt->nama_rt }}</option>@endforeach
                    </select>
                </div>
                @else
                <div>
                    <label class="block text-sm font-semibold mb-1">RT</label>
                    <div class="px-3 py-2 bg-green-50 border border-green-200 rounded-md text-sm font-semibold">{{ auth()->user()->rt->kode_rt }}</div>
                    <input type="hidden" name="rt_id" value="{{ auth()->user()->rt_id }}">
                </div>
                @endif
                <div>
                    <label class="block text-sm font-semibold mb-1">Jenis Periode *</label>
                    <select name="periode_type" class="w-full px-3 py-2 border rounded-md text-sm" required>
                        <option value="monthly">Bulanan (2026-01)</option>
                        <option value="weekly">Mingguan (2026-W01)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Periode *</label>
                    <input type="text" name="periode" placeholder="2026-09 atau 2026-W37" class="w-full px-3 py-2 border rounded-md text-sm" required value="{{ now()->format('Y-m') }}">
                    <p class="text-xs text-gray-500 mt-1">Bulanan: YYYY-MM, Mingguan: YYYY-Wxx</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Nominal *</label>
                    <input type="number" name="nominal" value="20000" class="w-full px-3 py-2 border rounded-md text-sm" required>
                </div>
                <div class="flex gap-2 justify-end pt-2">
                    <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="px-4 py-1.5 text-sm bg-gray-200 rounded-md">Batal</button>
                    <button type="submit" class="px-4 py-1.5 text-sm bg-emerald-600 text-white rounded-md">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
