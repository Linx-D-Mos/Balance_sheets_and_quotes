@props(['active' => false])

<button
    type="button"
    {{ $attributes->merge([
        'class' => 'relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden ' . ($active ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-700')
    ]) }}
>
    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow-xs transition duration-200 ease-in-out {{ $active ? 'translate-x-4' : 'translate-x-0' }}"></span>
</button>
