@props(['active' => false])

@php
$classes = ($active ?? false)
    ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-secondary text-start text-base font-medium text-white bg-primary-700 focus:outline-none transition duration-150 ease-in-out'
    : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-white hover:text-secondary-200 hover:bg-primary-700 hover:border-secondary focus:outline-none focus:text-secondary-200 focus:bg-primary-700 focus:border-secondary transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
