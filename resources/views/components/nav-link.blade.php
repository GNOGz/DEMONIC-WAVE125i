@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center my-1 px-3 py-2 border-b-2 border-indigo-400 text-2xl font-medium leading-5 text-gray-900 bg-[#1100FF] text-white focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out rounded-md'
            : 'inline-flex items-center my-1 px-3 py-2 border-b-2 border-transparent text-2xl font-medium leading-5 text-gray-500 hover:text-white hover:bg-[#1100FF] focus:outline-none focus:text-white focus:bg-blue-600 transition duration-150 ease-in-out rounded-md';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
