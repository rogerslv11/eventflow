@props([
    'icon' => 'calendar',
    'title' => 'Nada por aqui',
    'description' => null,
])

<div class="card p-12 text-center animate-fade-in">
    <div class="relative w-16 h-16 mx-auto mb-5">
        <div class="absolute inset-0 rounded-2xl bg-violet-500/10 border border-violet-500/20"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <x-icon :name="$icon" class="w-7 h-7 text-violet-400/70" />
        </div>
    </div>
    <h3 class="text-base font-semibold text-zinc-100">{{ $title }}</h3>
    @if($description)
        <p class="mt-1.5 text-sm text-zinc-500 max-w-sm mx-auto">{{ $description }}</p>
    @endif
    @if(isset($action))
        <div class="mt-6 flex justify-center">
            {{ $action }}
        </div>
    @endif
</div>
