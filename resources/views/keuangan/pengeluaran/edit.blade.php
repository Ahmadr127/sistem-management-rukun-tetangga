@extends('layouts.app')
@section('title', 'Edit Pengeluaran')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card>
        <x-slot name="title">Edit Pengeluaran</x-slot>
        <x-slot name="actions"><a href="{{ route('keuangan.pengeluaran.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        @include('keuangan._form', [
            'action' => route('keuangan.update', $keuangan),
            'method' => 'PUT',
            'modal' => 'edit-pengeluaran',
            'keuangan' => $keuangan,
            'categories' => $categories,
            'submitLabel' => 'Update',
            'submitClass' => 'bg-red-600',
            'prefix' => 'pengeluaranEditPage',
            'cancelUrl' => route('keuangan.pengeluaran.index'),
            'jenisDefault' => 'PENGELUARAN',
            'showJenisSelect' => true,
        ])
    </x-card>
</div>
@endsection
