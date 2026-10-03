@extends('layouts.app')
@section('title', 'Tambah Pengeluaran')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card>
        <x-slot name="title">Tambah Pengeluaran</x-slot>
        <x-slot name="actions"><a href="{{ route('keuangan.pengeluaran.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        @include('keuangan._form', [
            'action' => route('keuangan.store'),
            'method' => 'POST',
            'modal' => 'create-pengeluaran',
            'keuangan' => null,
            'categories' => $categories,
            'submitLabel' => 'Simpan',
            'submitClass' => 'bg-red-600',
            'prefix' => 'pengeluaranCreatePage',
            'cancelUrl' => route('keuangan.pengeluaran.index'),
            'jenisDefault' => 'PENGELUARAN',
            'showJenisSelect' => false,
        ])
    </x-card>
</div>
@endsection
