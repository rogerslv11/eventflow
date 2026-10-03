@props([
    'title' => null,
    'description' => null,
    'href' => null,
    'icon' => 'calendar',
    'iconBg' => 'primary',
    'value' => null,
    'trend' => null,
    'trendDirection' => 'up', // up, down, neutral
    'footer' => null,
])

@php
    $bgClasses = [
        'primary' => 'bg-violet-500/10 border-violet-500/25',
        'success' => 'bg-emerald-500/10 border-emerald-500/25',
        'warning' => 'bg-amber-500/10 border-amber-500/25',
        'danger' => 'bg-red-500/10 border-red-500/25',
        'info' => 'bg-blue-500/10 border-blue-500/25',
        'pink' => 'bg-pink-500/10 border-pink-500/25',
    ];
    $textClasses = [
        'primary' => 'text-violet-400',
        'success' => 'text-emerald-400',
        'warning' => 'text-amber-400',
        'danger' => 'text-red-400',
        'info' => 'text-blue-400',
        'pink' => 'text-pink-400',
    ];
    $bg = $bgClasses[$iconBg] ?? $bgClasses['primary'];
    $tc = $textClasses[$iconBg] ?? $textClasses['primary'];
@endphp

<div class="card card-hover group relative overflow-hidden p-5">
    <div class="pointer-events-none absolute -right-8 -top-8 h-28 w-28 rounded-full bg-violet-500/[0.07] blur-2xl transition-opacity group-hover:opacity-100"></div>
    <div class="relative flex items-start justify-between gap-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl border {{ $bg }}">
            <x-icon :name="$icon" class="h-5 w-5 {{ $tc }}" />
        </div>
        @if($trend)
            <div class="flex items-center gap-1 text-xs font-medium
                {{ $trendDirection === 'up' ? 'text-emerald-400' : '' }}
                {{ $trendDirection === 'down' ? 'text-red-400' : '' }}
                {{ $trendDirection === 'neutral' ? 'text-zinc-400' : '' }}
            ">
                <x-icon :name="$trendDirection === 'up' ? 'trending-up' : ($trendDirection === 'down' ? 'trending-down' : 'minus')" class="h-3.5 w-3.5" />
                {{ $trend }}
            </div>
        @endif
    </div>

    <div class="relative mt-4 space-y-1">
        <div class="text-[1.65rem] font-semibold leading-8 tracking-tight text-zinc-50 tabular-nums">{{ $value ?? $slot }}</div>
        @if($title)
            <div class="text-[0.8125rem] text-zinc-500">{{ $title }}</div>
        @endif
        @if($description)
            <div class="text-xs text-zinc-600">{{ $description }}</div>
        @endif
    </div>

    @if($footer)
        <div class="relative mt-4 border-t border-zinc-800/80 pt-3 text-xs">
            {{ $footer }}
        </div>
    @endif

    @if($href)
        <a href="{{ $href }}" class="absolute inset-0" aria-label="{{ $title }}"></a>
    @endif
</div>
