@extends('layouts.app')

@section('title', 'Tambah Pengaturan')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Tambah Pengaturan Baru</h2>
            <a href="{{ route('settings.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Kembali
            </a>
        </div>

        <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('settings._form')
            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
