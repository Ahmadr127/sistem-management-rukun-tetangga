@extends('layouts.app')
@section('title', 'Edit Pemasukan')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card>
        <x-slot name="title">Edit Pemasukan</x-slot>
        <x-slot name="actions"><a href="{{ route('keuangan.pemasukan.index') }}" class="text-sm px-3 py-1.5 border rounded-md">Kembali</a></x-slot>
        @include('keuangan._form', [
            'action' => route('keuangan.update', $keuangan),
            'method' => 'PUT',
            'modal' => 'edit-pemasukan',
            'keuangan' => $keuangan,
            'categories' => $categories,
            'submitLabel' => 'Update',
            'submitClass' => 'bg-green-600',
            'prefix' => 'pemasukanEditPage',
            'cancelUrl' => route('keuangan.pemasukan.index'),
            'jenisDefault' => 'PEMASUKAN',
            'showJenisSelect' => true,
        ])
    </x-card>
</div>
@endsection
