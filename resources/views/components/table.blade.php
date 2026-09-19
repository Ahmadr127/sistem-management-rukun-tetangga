@props([
    'columns' => [],
    'pagination' => null,
    'empty' => 'Tidak ada data.',
    'perPage' => null,
    'perPageOptions' => [5, 10, 25, 50, 100],
    'accent' => null,
])

@php
    $perPage = $perPage ?? (int) request('per_page', 10);
    $accentHeader = [
        'teal' => 'bg-teal-50 text-teal-800 border-teal-100',
        'green' => 'bg-green-50 text-green-800 border-green-100',
        'blue' => 'bg-blue-50 text-blue-800 border-blue-100',
        'purple' => 'bg-purple-50 text-purple-800 border-purple-100',
        'amber' => 'bg-amber-50 text-amber-800 border-amber-100',
        'red' => 'bg-red-50 text-red-800 border-red-100',
        'indigo' => 'bg-indigo-50 text-indigo-800 border-indigo-100',
    ][$accent] ?? 'bg-gray-50 text-gray-700';
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden']) }}>
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-max">
            <thead>
                <tr class="{{ $accentHeader }} border-b">
                    @foreach($columns as $column)
                        <th class="px-4 py-2.5 text-left font-semibold whitespace-nowrap {{ $accent ? '' : 'text-gray-700' }}">
                            {{ is_array($column) ? ($column['label'] ?? '') : $column }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @if(!$slot->isEmpty())
                    {{ $slot }}
                @else
                    <tr>
                        <td colspan="{{ count($columns) ?: 1 }}" class="px-4 py-8 text-center text-gray-500">{{ $empty }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($pagination)
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 px-4 py-3 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <label for="per-page-{{ Str::slug($pagination->path()) }}" class="text-xs text-gray-500">Tampilkan</label>
                <select
                    id="per-page-{{ Str::slug($pagination->path()) }}"
                    onchange="const p = new URLSearchParams(window.location.search); p.set('per_page', this.value); p.delete('page'); window.location = window.location.pathname + '?' + p.toString();"
                    class="px-2 py-1 text-xs border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-sp-primary/20 focus:border-sp-primary"
                >
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                <label for="per-page-{{ Str::slug($pagination->path()) }}" class="text-xs text-gray-500">per halaman</label>
            </div>
            <p class="text-xs text-gray-500">
                Menampilkan {{ $pagination->firstItem() ?? 0 }}–{{ $pagination->lastItem() ?? 0 }} dari {{ $pagination->total() }} data
            </p>
            <div class="text-sm">{{ $pagination->links() }}</div>
        </div>
    @endif
</div>
