@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
])

<div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0">
        @if($eyebrow)
            <div class="eyebrow mb-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-violet-400"></span>
                {{ $eyebrow }}
            </div>
        @endif
        <h1 class="page-title">{{ $title }}</h1>
        @if($subtitle)
            <p class="page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex shrink-0 items-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
