@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="w-full mx-auto" x-data="{
    ...tableFilter({
        search: '{{ request('search') }}'
    })
}">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Pengaturan Sistem</h2>
                    <p class="text-sm text-gray-500 mt-1">Kelola logo, nama sistem & pengaturan lainnya</p>
                </div>
                @if(auth()->user()->hasPermission('manage_settings'))
                <a href="{{ route('settings.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Tambah Pengaturan
                </a>
                @endif
            </div>
        </div>

        <!-- Table Filter Component -->
        <x-table-filter
            search-placeholder="Cari key / nama pengaturan..."
        />

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pengaturan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nilai / Pratinjau
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($settings as $setting)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $setting->display_name }}</div>
                            <div class="text-xs font-mono text-gray-500">{{ $setting->key }}</div>
                            @if($setting->is_system)
                            <span class="mt-1 inline-block text-[10px] px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 font-semibold">SISTEM</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-block text-[11px] px-2 py-0.5 rounded-full font-semibold @if($setting->type=='image') bg-purple-100 text-purple-800 @elseif($setting->type=='textarea') bg-blue-100 text-blue-800 @else bg-gray-100 text-gray-700 @endif">{{ strtoupper($setting->type) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($setting->isImage())
                                @if($setting->valueUrl())
                                <img src="{{ $setting->valueUrl() }}" alt="{{ $setting->display_name }}" class="h-10 w-auto object-contain bg-gray-50 border rounded px-1">
                                @else
                                <span class="text-sm text-gray-400 italic">Belum ada logo</span>
                                @endif
                            @else
                            <div class="text-sm text-gray-700 max-w-xs truncate">{{ $setting->value ?? '-' }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('settings.show', $setting) }}" class="text-green-600 hover:text-green-900 mr-3">Detail</a>
                            @if(auth()->user()->hasPermission('manage_settings'))
                            <a href="{{ route('settings.edit', $setting) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            @if(!$setting->is_system)
                            <form action="{{ route('settings.destroy', $setting) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus pengaturan ini?')">
                                    Hapus
                                </button>
                            </form>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data pengaturan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($settings->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $settings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
