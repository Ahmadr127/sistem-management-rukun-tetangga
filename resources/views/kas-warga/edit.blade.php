@extends('layouts.app')
@section('title', 'Edit Jenis Kas')
@section('content')
<div class="max-w-2xl mx-auto">
    <x-card title="Edit Jenis Kas" subtitle="{{ $kasJenis->nama }}" accent="green">
        @include('kas-warga._form', [
            'action' => route('kas-warga.update', $kasJenis),
            'method' => 'PUT',
            'modal' => 'edit-kas',
            'kasJenis' => $kasJenis,
            'rts' => $rts,
            'submitLabel' => 'Perbarui',
            'prefix' => 'kasEditPage',
            'cancelUrl' => route('kas-warga.show', $kasJenis),
        ])
    </x-card>
</div>
@endsection
