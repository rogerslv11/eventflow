<x-layouts.public>
    {{-- Header --}}
    <header class="sticky top-0 z-40 border-b border-zinc-800/80 bg-zinc-950/85 backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 lg:px-6">
            <a href="{{ route('public.events.index') }}" class="flex items-center gap-2.5">
                <div class="relative flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-violet-400 via-violet-600 to-violet-800 shadow-[0_4px_16px_-4px_rgb(124_58_237/0.7)]">
                    <x-icon name="sparkles" class="h-4 w-4 text-white" />
                    <div class="absolute inset-0 rounded-xl ring-1 ring-inset ring-white/20"></div>
                </div>
                <span class="text-[0.9375rem] font-semibold tracking-tight text-zinc-50">EventFlow</span>
            </a>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="btn btn-secondary btn-sm">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Entrar</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Criar conta</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Banner --}}
    <section class="relative h-[26rem] overflow-hidden">
        <img src="{{ $event->bannerUrl() }}" alt="{{ $event->name }}" class="h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/60 to-zinc-950/20"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-violet-950/40 via-transparent to-transparent"></div>

        <div class="absolute inset-x-0 bottom-0">
            <div class="mx-auto max-w-6xl px-4 pb-8 lg:px-6">
                <div class="mb-3 flex flex-wrap items-center gap-2">
                    @if($event->category)
                        <span class="badge badge-primary bg-zinc-950/60 backdrop-blur">
                            <x-icon :name="$event->category->icon ?? 'tag'" class="h-3 w-3" />
                            {{ $event->category->name }}
                        </span>
                    @endif
                    <span class="badge badge-success bg-zinc-950/60 backdrop-blur">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Inscrições abertas
                    </span>
                </div>
                <h1 class="mb-3 max-w-3xl text-4xl font-semibold tracking-tight text-white lg:text-5xl">{{ $event->name }}</h1>
                <div class="flex flex-wrap items-center gap-2.5 text-sm text-zinc-300">
                    <span class="flex items-center gap-1.5 rounded-lg bg-zinc-950/60 px-2.5 py-1.5 backdrop-blur">
                        <x-icon name="calendar" class="h-4 w-4 text-violet-300" />
                        {{ $event->starts_at->format('d M Y') }} às {{ $event->starts_at->format('H:i') }}
                    </span>
                    <span class="flex items-center gap-1.5 rounded-lg bg-zinc-950/60 px-2.5 py-1.5 backdrop-blur">
                        <x-icon name="map-pin" class="h-4 w-4 text-violet-300" />
                        {{ $event->venue_name ?: 'Online' }}@if($event->city), {{ $event->city }}/{{ $event->state }}@endif
                    </span>
                    @if($event->organization)
                        <span class="hidden items-center gap-1.5 rounded-lg bg-zinc-950/60 px-2.5 py-1.5 backdrop-blur sm:flex">
                            <x-icon name="building" class="h-4 w-4 text-violet-300" />
                            {{ $event->organization->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <main class="max-w-6xl mx-auto px-4 lg:px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- About --}}
                <section class="card p-6">
                    <h2 class="text-xl font-semibold text-zinc-100 mb-4">Sobre o evento</h2>
                    <p class="text-zinc-300 leading-relaxed">{{ $event->description }}</p>
                    @if($event->content)
                        <div class="mt-4 text-zinc-300 leading-relaxed prose prose-invert max-w-none">
                            {!! nl2br(e($event->content)) !!}
                        </div>
                    @endif
                </section>

                {{-- Schedule --}}
                @if($event->schedules->count() > 0)
                    <section class="card p-6">
                        <h2 class="text-xl font-semibold text-zinc-100 mb-4">Programação</h2>
                        <div class="space-y-3">
                            @foreach($event->schedules->take(10) as $schedule)
                                <div class="flex gap-4 p-4 rounded-lg bg-zinc-900/50">
                                    <div class="text-center min-w-16">
                                        <div class="text-base font-semibold text-zinc-100">{{ $schedule->starts_at->format('H:i') }}</div>
                                        <div class="text-xs text-zinc-500">{{ $schedule->starts_at->format('d/m') }}</div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="badge badge-{{ $schedule->type->color() }}">{{ $schedule->type->label() }}</span>
                                            @if($schedule->room)
                                                <span class="text-xs text-zinc-500">• {{ $schedule->room }}</span>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-medium text-zinc-100">{{ $schedule->title }}</h4>
                                        @if($schedule->speaker)
                                            <p class="text-xs text-zinc-500 mt-1">com {{ $schedule->speaker->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Speakers --}}
                @if($event->speakers->count() > 0)
                    <section class="card p-6">
                        <h2 class="text-xl font-semibold text-zinc-100 mb-4">Palestrantes</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($event->speakers as $speaker)
                                <div class="text-center">
                                    <img src="{{ $speaker->avatarUrl() }}" alt="{{ $speaker->name }}" class="w-16 h-16 rounded-full mx-auto mb-2 border-2 border-zinc-800">
                                    <h4 class="text-sm font-medium text-zinc-100">{{ $speaker->name }}</h4>
                                    @if($speaker->role)
                                        <p class="text-xs text-zinc-500 mt-0.5">{{ $speaker->role }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Sponsors --}}
                @if($event->sponsors->count() > 0)
                    <section class="card p-6">
                        <h2 class="text-xl font-semibold text-zinc-100 mb-4">Patrocinadores</h2>
                        <div class="grid grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($event->sponsors->where('is_active', true) as $sponsor)
                                    <img src="{{ $sponsor->logoUrl() }}" alt="{{ $sponsor->name }}" class="w-full h-16 object-contain bg-zinc-900 border border-zinc-800 rounded-lg p-2">
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Reviews --}}
                @livewire('public.event-review-form', ['event' => $event])
            </div>

            {{-- Sidebar --}}
            <aside class="lg:col-span-1">
                <div class="card-elevated sticky top-20 overflow-hidden">
                    <div class="h-1 bg-gradient-to-r from-violet-500 via-violet-600 to-violet-800"></div>
                    <div class="p-6">
                    <div class="mb-1 flex items-center justify-between">
                        <h3 class="text-lg font-semibold tracking-tight text-zinc-50">Ingressos</h3>
                        <span class="badge badge-success badge-dot">Disponível</span>
                    </div>
                    <p class="mb-4 text-[0.8125rem] text-zinc-500">Garanta sua vaga — vagas limitadas</p>

                    <div class="mb-5 space-y-2.5">
                        @foreach($event->ticketTypes->where('is_active', true) as $tt)
                            <div class="rounded-xl border border-zinc-800 bg-zinc-900/60 p-4 transition-colors hover:border-zinc-700">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="truncate font-medium text-zinc-100">{{ $tt->name }}</div>
                                        @if($tt->description)
                                            <div class="mt-0.5 line-clamp-2 text-xs text-zinc-500">{{ $tt->description }}</div>
                                        @endif
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <div class="gradient-text text-base font-semibold tabular-nums">
                                            {{ $tt->formattedPrice() }}
                                        </div>
                                        <div class="mt-0.5 text-xs tabular-nums text-zinc-500">
                                            {{ $tt->available() }} disponíveis
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ route('public.checkout', $event->slug) }}" class="btn btn-primary btn-lg w-full">
                        <x-icon name="shopping-cart" class="h-4 w-4" />
                        Comprar ingresso
                    </a>

                    <div class="mt-4">
                        @livewire('public.waitlist-form', ['event' => $event])
                    </div>

                    <div class="mt-5 space-y-2.5 border-t border-zinc-800/80 pt-5 text-xs text-zinc-500">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-500/10"><x-icon name="check-circle" class="h-3.5 w-3.5 text-emerald-400" /></span>
                            <span>Compra 100% segura</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-500/10"><x-icon name="ticket" class="h-3.5 w-3.5 text-emerald-400" /></span>
                            <span>Ingresso digital com QR Code</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-500/10"><x-icon name="calendar" class="h-3.5 w-3.5 text-emerald-400" /></span>
                            <span>Lembrete automático</span>
                        </div>
                    </div>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="mt-12 border-t border-zinc-800/80">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 px-4 py-6 text-xs text-zinc-600 sm:flex-row lg:px-6">
            <span>© {{ date('Y') }} EventFlow — Plataforma completa para gestão de eventos.</span>
            <span>Compra segura · Ingresso digital · Suporte dedicado</span>
        </div>
    </footer>
</x-layouts.public>