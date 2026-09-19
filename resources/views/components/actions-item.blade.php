@props([
    'href' => null,
    'icon' => null,
    'label' => null,
    'color' => 'text-gray-700 hover:bg-gray-50 hover:text-sp-primary',
])

@if($href)
    <a href="{{ $href }}" title="{{ $label ?? '' }}" {{ $attributes->merge(['class' => 'action-item flex items-center gap-2 px-3 py-2 text-sm font-medium ' . $color]) }}>
        @if($icon)<i class="bi {{ $icon }} w-4 text-center flex-shrink-0"></i>@endif
        <span class="action-label">{{ $label ?? $slot }}</span>
    </a>
@else
    <button type="button" title="{{ $label ?? '' }}" {{ $attributes->merge(['class' => 'action-item w-full flex items-center gap-2 px-3 py-2 text-sm font-medium ' . $color]) }}>
        @if($icon)<i class="bi {{ $icon }} w-4 text-center flex-shrink-0"></i>@endif
        <span class="action-label">{{ $label ?? $slot }}</span>
    </button>
@endif
