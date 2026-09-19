@extends('layouts.app')
@section('title', 'Detail RT')
@section('content')
<div class="space-y-4 max-w-4xl mx-auto">
    <x-card title="{{ $rt->kode_rt }}" subtitle="{{ $rt->nama_rt }}" accent="teal">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div><div class="text-xs text-gray-500">Kode RT</div><div class="font-semibold">{{ $rt->kode_rt }}</div></div>
            <div><div class="text-xs text-gray-500">Nama</div><div class="font-semibold">{{ $rt->nama_rt }}</div></div>
            <div><div class="text-xs text-gray-500">Status</div>@if($rt->is_active)<span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">Aktif</span>@else<span class="px-2 py-0.5 text-xs bg-gray-200 rounded-full">Nonaktif</span>@endif</div>
            <div><div class="text-xs text-gray-500">Warga</div><div class="font-semibold">{{ $rt->warga_count }} warga</div></div>
            <div><div class="text-xs text-gray-500">KK</div><div class="font-semibold">{{ $rt->kartu_keluarga_count }} KK</div></div>
            <div><div class="text-xs text-gray-500">Users</div><div class="font-semibold">{{ $rt->users_count }} user</div></div>
            <div class="col-span-2"><div class="text-xs text-gray-500">Keterangan</div><div>{{ $rt->keterangan ?? '-' }}</div></div>
        </div>
        <div class="mt-4 p-3 bg-teal-50 border border-teal-100 rounded">
            <div class="text-xs font-semibold text-teal-800 mb-1">Alamat Wilayah RT (Master)</div>
            @if($rt->alamatRt)
                <div class="text-xs text-gray-700">{{ $rt->alamatRt->alamat ?? '-' }}, RW {{ $rt->alamatRt->rw ?? '-' }}, {{ $rt->alamatRt->kelurahan ?? '-' }}, {{ $rt->alamatRt->kecamatan ?? '-' }}, {{ $rt->alamatRt->kota ?? '-' }}, {{ $rt->alamatRt->provinsi ?? '-' }} {{ $rt->alamatRt->kode_pos ?? '' }}</div>
                <a href="{{ route('alamat-rt.edit', $rt->alamatRt) }}" class="inline-block mt-2 text-xs text-teal-700 underline">Edit Alamat</a>
            @else
                <div class="text-xs text-amber-700">Alamat RT belum dikonfigurasi.</div>
                <a href="{{ route('alamat-rt.create') }}" class="inline-block mt-2 px-2 py-1 bg-teal-600 text-white rounded text-xs">Konfigurasi Alamat</a>
            @endif
        </div>
        <div class="flex gap-2 mt-4">
            <a href="{{ route('rts.edit', $rt) }}" class="px-3 py-1.5 text-sm bg-teal-600 text-white rounded-md">Edit</a>
            <a href="{{ route('rts.index') }}" class="px-3 py-1.5 text-sm bg-gray-200 rounded-md">Kembali</a>
        </div>
    </x-card>

    @if($rt->users->count())
    <x-card title="User RT" padding="false" accent="teal">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-teal-50"><tr><th class="px-3 py-2 text-left">No</th><th class="px-3 py-2 text-left">Nama</th><th class="px-3 py-2 text-left">Username</th><th class="px-3 py-2 text-left">Role</th></tr></thead>
                <tbody class="divide-y">@foreach($rt->users as $u)<tr><td class="px-3 py-2">{{ $loop->iteration }}</td><td class="px-3 py-2">{{ $u->name }}</td><td class="px-3 py-2">{{ $u->username }}</td><td class="px-3 py-2">{{ $u->role->display_name ?? '-' }}</td></tr>@endforeach</tbody>
            </table>
        </div>
    </x-card>
    @endif
</div>
@endsection
