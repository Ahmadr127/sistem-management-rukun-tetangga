@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
    'footer' => null,
    'padding' => true,
    'accent' => null,
])

@php
    $accentBorder = [
        'teal' => 'border-t-2 border-t-teal-500',
        'green' => 'border-t-2 border-t-green-500',
        'blue' => 'border-t-2 border-t-blue-500',
        'purple' => 'border-t-2 border-t-purple-500',
        'amber' => 'border-t-2 border-t-amber-500',
        'red' => 'border-t-2 border-t-red-500',
        'indigo' => 'border-t-2 border-t-indigo-500',
    ][$accent] ?? '';
@endphp
<div {{ $attributes->merge(['class' => "bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden $accentBorder"]) }}>
    @if($title || !empty($actions))
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <div class="min-w-0">
                @if($title)
                    <h5 class="font-bold text-sp-navy">{{ $title }}</h5>
                @endif
                @if($subtitle)
                    <p class="text-xs text-gray-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if(!empty($actions))
                <div class="flex items-center gap-2 flex-shrink-0">{{ $actions }}</div>
            @endif
        </div>
    @endif

    <div @if($padding) class="p-4" @endif>
        {{ $slot }}
    </div>

    @if(!empty($footer))
        <div class="px-4 py-3 border-t border-gray-100">{{ $footer }}</div>
    @endif
</div>
