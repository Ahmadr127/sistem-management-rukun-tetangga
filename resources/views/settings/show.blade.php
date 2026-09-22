@extends('layouts.app')

@section('title', 'Detail Pengaturan')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">{{ $setting->display_name }}</h2>
            <div class="flex gap-2">
                @if(auth()->user()->hasPermission('manage_settings'))
                <a href="{{ route('settings.edit', $setting) }}" class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                @endif
                <a href="{{ route('settings.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Kembali
                </a>
            </div>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="border rounded-md p-3">
                <dt class="font-semibold text-gray-500">Key</dt>
                <dd class="font-mono text-gray-900 mt-1">{{ $setting->key }}</dd>
            </div>
            <div class="border rounded-md p-3">
                <dt class="font-semibold text-gray-500">Tipe</dt>
                <dd class="mt-1"><span class="inline-block text-[11px] px-2 py-0.5 rounded-full font-semibold bg-gray-100 text-gray-700">{{ strtoupper($setting->type) }}</span>
                @if($setting->is_system)
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 font-semibold">SISTEM</span>
                @endif
                </dd>
            </div>
            <div class="border rounded-md p-3 md:col-span-2">
                <dt class="font-semibold text-gray-500">Nilai</dt>
                <dd class="mt-1">
                    @if($setting->isImage())
                        @if($setting->valueUrl())
                        <img src="{{ $setting->valueUrl() }}" alt="{{ $setting->display_name }}" class="h-24 w-auto object-contain bg-gray-50 border rounded p-1">
                        @else
                        <span class="text-gray-400 italic">Belum ada file</span>
                        @endif
                    @else
                    <span class="text-gray-900">{{ $setting->value ?? '-' }}</span>
                    @endif
                </dd>
            </div>
            <div class="border rounded-md p-3 md:col-span-2">
                <dt class="font-semibold text-gray-500">Deskripsi</dt>
                <dd class="text-gray-900 mt-1">{{ $setting->description ?? '-' }}</dd>
            </div>
            <div class="border rounded-md p-3">
                <dt class="font-semibold text-gray-500">Diperbarui</dt>
                <dd class="text-gray-900 mt-1">{{ $setting->updated_at?->locale('id')->isoFormat('dddd, D MMM YYYY HH:mm') }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection
