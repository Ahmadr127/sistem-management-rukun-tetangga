@extends('layouts.app')
@section('title', 'Tambah Pemasukan')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card>
        <x-slot name="title">Tambah Pemasukan</x-slot>
        <x-slot name="actions"><a href="{{ route('keuangan.pemasukan.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        @include('keuangan._form', [
            'action' => route('keuangan.store'),
            'method' => 'POST',
            'modal' => 'create-pemasukan',
            'keuangan' => null,
            'categories' => $categories,
            'submitLabel' => 'Simpan',
            'submitClass' => 'bg-green-600',
            'prefix' => 'pemasukanCreatePage',
            'cancelUrl' => route('keuangan.pemasukan.index'),
            'jenisDefault' => 'PEMASUKAN',
            'showJenisSelect' => false,
        ])
    </x-card>
</div>
@endsection
