@props([
    'label',
    'value',
    'unit' => null,
    'subtext' => null,
    'icon' => null,
    'actionIcon' => null,
    'actionWireClick' => null,
    'actionTitle' => null,
])

<div {{ $attributes->merge(['class' => 'p-5 bg-white dark:bg-gray-900 rounded-xl border border-gray-200/80 dark:border-gray-800 shadow-xs transition']) }}>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            @if($icon)
                <x-dynamic-component :component="$icon" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
            @endif
            <span>{{ $label }}</span>
        </div>

        @if($actionWireClick && $actionIcon)
            <button
                type="button"
                wire:click="{{ $actionWireClick }}"
                wire:loading.attr="disabled"
                title="{{ $actionTitle ?? 'Recalcular' }}"
                class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition cursor-pointer"
            >
                <x-dynamic-component :component="$actionIcon" class="w-4 h-4" wire:loading.class="animate-spin" />
            </button>
        @elseif($actionIcon)
            <x-dynamic-component :component="$actionIcon" class="w-4 h-4 text-gray-400" />
        @endif
    </div>

    <div class="mt-2 flex items-baseline gap-2">
        <span class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white">
            {{ $value }}
        </span>
        @if($unit)
            <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $unit }}</span>
        @endif
    </div>

    @if($subtext)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ $subtext }}
        </p>
    @endif
</div>
