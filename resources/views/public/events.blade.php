<x-layouts.public>
    <header class="sticky top-0 z-40 border-b border-zinc-800/80 bg-zinc-950/85 backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 lg:px-6">
            <a href="{{ route('public.events.index') }}" class="flex items-center gap-2.5">
                <div class="relative flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-violet-400 via-violet-600 to-violet-800 shadow-[0_4px_16px_-4px_rgb(124_58_237/0.7)]">
                    <x-icon name="sparkles" class="h-4 w-4 text-white" />
                    <div class="absolute inset-0 rounded-xl ring-1 ring-inset ring-white/20"></div>
                </div>
                <span class="text-[0.9375rem] font-semibold tracking-tight text-zinc-50">EventFlow</span>
            </a>
            @auth
                <a href="{{ route('dashboard.index') }}" class="btn btn-secondary btn-sm">Dashboard</a>
            @else
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Entrar</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Criar conta</a>
                </div>
            @endauth
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-10 lg:px-6 lg:py-14">
        <div class="mb-10 max-w-2xl">
            <div class="eyebrow mb-3">
                <span class="h-1.5 w-1.5 rounded-full bg-violet-400"></span>
                Agenda pública
            </div>
            <h1 class="text-3xl font-semibold tracking-tight text-zinc-50 sm:text-4xl">Eventos disponíveis</h1>
            <p class="mt-2 text-[0.9375rem] text-zinc-400">Descubra eventos incríveis e garanta sua vaga com ingresso digital e check-in rápido.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse($events as $event)
                <a href="{{ route('public.event.show', $event->slug) }}" class="card card-hover group overflow-hidden">
                    <div class="relative h-48 overflow-hidden bg-zinc-900">
                        <img src="{{ $event->bannerUrl() }}" alt="{{ $event->name }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/20 to-transparent"></div>
                        @if($event->is_featured)
                            <span class="badge badge-warning absolute right-3 top-3 shadow-lg">
                                <x-icon name="star" class="h-3 w-3" /> Destaque
                            </span>
                        @endif
                        @if($event->category)
                            <span class="badge badge-neutral absolute left-3 top-3 bg-zinc-950/70 backdrop-blur">{{ $event->category->name }}</span>
                        @endif
                        <div class="absolute inset-x-3 bottom-3 flex items-center gap-1.5 text-xs font-medium text-zinc-200">
                            <span class="flex items-center gap-1.5 rounded-lg bg-zinc-950/70 px-2 py-1 backdrop-blur">
                                <x-icon name="calendar" class="h-3.5 w-3.5 text-violet-300" />
                                {{ $event->starts_at->format('d M Y') }} · {{ $event->starts_at->format('H:i') }}
                            </span>
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold tracking-tight text-zinc-100 transition-colors group-hover:text-violet-300">{{ $event->name }}</h3>
                        <p class="mb-4 mt-1 line-clamp-2 text-[0.8125rem] leading-relaxed text-zinc-500">{{ $event->description }}</p>
                        <div class="flex items-center justify-between border-t border-zinc-800/80 pt-4 text-xs text-zinc-500">
                            <div class="flex items-center gap-1.5">
                                <x-icon name="map-pin" class="h-3.5 w-3.5 text-zinc-600" />
                                {{ $event->venue_name ?: 'Online' }}@if($event->city), {{ $event->city }}/{{ $event->state }}@endif
                            </div>
                            <span class="font-medium text-violet-300 transition-transform group-hover:translate-x-0.5">Ver detalhes →</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full">
                    <x-empty-state icon="calendar" title="Nenhum evento disponível" description="Volte em breve — novos eventos são publicados toda semana." />
                </div>
            @endforelse
        </div>

        @if($events->hasPages())
            <div class="mt-10">{{ $events->links() }}</div>
        @endif
    </main>

    <footer class="border-t border-zinc-800/80">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 px-4 py-6 text-xs text-zinc-600 sm:flex-row lg:px-6">
            <span>© {{ date('Y') }} EventFlow — Plataforma completa para gestão de eventos.</span>
            <span>Compra segura · Ingresso digital · Suporte dedicado</span>
        </div>
    </footer>
</x-layouts.public>
