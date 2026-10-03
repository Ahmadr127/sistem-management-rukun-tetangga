@extends('layouts.app')
@section('title', 'Tambah Jenis Kas')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Tambah Jenis Kas" subtitle="Input bulanan / tahunan / mingguan, nominal, dan target KK atau perorangan" accent="green">
        @include('kas-warga._form', [
            'action' => route('kas-warga.store'),
            'method' => 'POST',
            'modal' => 'create-kas',
            'kasJenis' => null,
            'rts' => $rts,
            'submitLabel' => 'Simpan & Buka Tabel',
            'prefix' => 'kasCreate',
            'cancelUrl' => route('kas-warga.index'),
            'defaults' => ['rt_id' => $selectedRt],
        ])
    </x-card>
</div>
@endsection
