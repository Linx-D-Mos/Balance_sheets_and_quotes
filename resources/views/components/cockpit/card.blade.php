@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'actionUrl' => null,
    'actionLabel' => null,
    'actionIcon' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-900 rounded-xl border border-gray-200/80 dark:border-gray-800 p-5 shadow-xs transition']) }}>
    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800/80">
        <div class="flex items-center gap-2">
            @if($icon)
                <x-dynamic-component :component="$icon" class="w-5 h-5 text-gray-400 dark:text-gray-500" />
            @endif
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
                @if($subtitle)
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if($actionUrl && $actionIcon)
            <a href="{{ $actionUrl }}" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-primary-600 dark:text-primary-400 transition">
                <x-dynamic-component :component="$actionIcon" class="w-5 h-5" />
            </a>
        @elseif($actionUrl && $actionLabel)
            <a href="{{ $actionUrl }}" class="text-primary-600 hover:text-primary-500 dark:text-primary-400 text-xs font-semibold transition">
                {{ $actionLabel }} →
            </a>
        @endif
    </div>

    <div class="mt-3">
        {{ $slot }}
    </div>
</div>
