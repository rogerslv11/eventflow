<div class="space-y-6">
    {{-- Header / Breadcrumbs --}}
    <nav class="flex items-center gap-1.5 text-[0.8125rem]" aria-label="Navegação">
        <a href="{{ route('dashboard.events.index') }}" class="text-zinc-500 transition-colors hover:text-zinc-200">
            Eventos
        </a>
        <x-icon name="chevron-right" class="h-3.5 w-3.5 text-zinc-700" />
        <span class="truncate font-medium text-zinc-200">{{ $event->name }}</span>
    </nav>

    {{-- Hero --}}
    <div class="card overflow-hidden">
        <div class="relative h-64 lg:h-80 bg-zinc-900">
            <img src="{{ $event->bannerUrl() }}" alt="{{ $event->name }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-zinc-950/40 to-transparent"></div>

            {{-- Status badges top --}}
            <div class="absolute top-4 left-4 flex items-center gap-2">
                <span class="badge badge-{{ event_status_badge($event->status->value) }} backdrop-blur bg-zinc-900/70">
                    <x-icon :name="event_status_icon($event->status->value)" class="w-3 h-3" />
                    {{ $event->status->label() }}
                </span>
                @if($event->is_featured)
                    <span class="badge badge-warning backdrop-blur bg-zinc-900/70">
                        <x-icon name="star" class="w-3 h-3" />
                        Destaque
                    </span>
                @endif
            </div>

            {{-- Actions top right --}}
            <div class="absolute top-4 right-4 flex items-center gap-2">
                <a href="{{ route('public.event.show', $event->slug) }}" target="_blank" class="btn btn-secondary backdrop-blur bg-zinc-900/70 text-sm">
                    <x-icon name="arrow-up-right" class="w-4 h-4" />
                    Ver página
                </a>
                <a href="{{ route('dashboard.events.edit', $event) }}" class="btn btn-primary text-sm">
                    <x-icon name="edit" class="w-4 h-4" />
                    Editar
                </a>
            </div>

            {{-- Bottom info --}}
            <div class="absolute bottom-0 inset-x-0 p-6">
                @if($event->category)
                    <span class="badge badge-primary mb-3 inline-flex">
                        <x-icon :name="$event->category->icon ?? 'tag'" class="w-3 h-3" />
                        {{ $event->category->name }}
                    </span>
                @endif
                <h1 class="text-3xl lg:text-4xl font-semibold text-white mb-2">{{ $event->name }}</h1>
                <div class="flex flex-wrap items-center gap-4 text-zinc-300 text-sm">
                    <div class="flex items-center gap-1.5">
                        <x-icon name="calendar" class="w-4 h-4" />
                        {{ $event->starts_at->format('d M Y, H:i') }}
                    </div>
                    @if($event->venue_name || $event->city)
                        <div class="flex items-center gap-1.5">
                            <x-icon name="map-pin" class="w-4 h-4" />
                            {{ $event->venue_name ?: $event->city }}
                            @if($event->city)/{{ $event->state }}@endif
                        </div>
                    @endif
                    @if($event->capacity)
                        <div class="flex items-center gap-1.5">
                            <x-icon name="users" class="w-4 h-4" />
                            {{ number_format($event->capacity) }} lugares
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-5 hover:border-violet-500/30 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center">
                    <x-icon name="ticket" class="w-5 h-5 text-violet-400" />
                </div>
            </div>
            <div class="text-2xl font-semibold text-zinc-100">{{ number_format($event->tickets_count) }}</div>
            <div class="text-xs text-zinc-500 mt-1">Ingressos vendidos</div>
            @if($event->capacity)
                @php
                    $pct = min(100, ($event->tickets_count / $event->capacity) * 100);
                @endphp
                <div class="mt-3 pt-3 border-t border-zinc-800">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-zinc-500">de {{ number_format($event->capacity) }}</span>
                        <span class="text-violet-400">{{ round($pct) }}%</span>
                    </div>
                    <div class="h-1 bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-violet-500 to-violet-700" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        <div class="card p-5 hover:border-violet-500/30 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                    <x-icon name="credit-card" class="w-5 h-5 text-emerald-400" />
                </div>
            </div>
            <div class="text-2xl font-semibold text-zinc-100">R$ {{ number_format($event->totalRevenue(), 0, ',', '.') }}</div>
            <div class="text-xs text-zinc-500 mt-1">Receita total</div>
            @if($event->tickets_count > 0)
                <div class="mt-3 pt-3 border-t border-zinc-800">
                    <div class="text-xs text-zinc-500">Ticket médio</div>
                    <div class="text-sm font-medium text-zinc-100 mt-0.5">
                        R$ {{ number_format($event->totalRevenue() / max($event->tickets_count, 1), 2, ',', '.') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="card p-5 hover:border-violet-500/30 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                    <x-icon name="check-circle" class="w-5 h-5 text-amber-400" />
                </div>
                <span class="badge badge-success">{{ $event->checkInRate() }}%</span>
            </div>
            <div class="text-2xl font-semibold text-zinc-100">{{ number_format($event->check_ins_count) }}</div>
            <div class="text-xs text-zinc-500 mt-1">Check-ins realizados</div>
            <div class="mt-3 pt-3 border-t border-zinc-800">
                <div class="text-xs text-zinc-500">Pendentes</div>
                <div class="text-sm font-medium text-zinc-100 mt-0.5">
                    {{ max(0, $event->tickets_count - $event->check_ins_count) }}
                </div>
            </div>
        </div>

        <div class="card p-5 hover:border-violet-500/30 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-pink-500/10 border border-pink-500/20 flex items-center justify-center">
                    <x-icon name="users" class="w-5 h-5 text-pink-400" />
                </div>
            </div>
            <div class="text-2xl font-semibold text-zinc-100">{{ number_format($event->orders_count) }}</div>
            <div class="text-xs text-zinc-500 mt-1">Pedidos</div>
            <div class="mt-3 pt-3 border-t border-zinc-800">
                <div class="text-xs text-zinc-500">Participantes</div>
                <div class="text-sm font-medium text-zinc-100 mt-0.5">
                    {{ number_format($event->participants_count) }}
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'info' }">
        <div class="border-b border-zinc-800 mb-4 flex items-center gap-1 overflow-x-auto">
            @php
                $tabs = [
                    ['id' => 'info', 'label' => 'Informações', 'count' => null],
                    ['id' => 'tickets', 'label' => 'Ingressos', 'count' => $event->ticketTypes->count()],
                    ['id' => 'schedule', 'label' => 'Programação', 'count' => $event->schedules->count()],
                    ['id' => 'speakers', 'label' => 'Palestrantes', 'count' => $event->speakers->count()],
                    ['id' => 'team', 'label' => 'Equipe', 'count' => $event->staff->count()],
                ];
            @endphp
            @foreach($tabs as $t)
                <button @click="tab = '{{ $t['id'] }}'"
                        :class="tab === '{{ $t['id'] }}' ? 'border-violet-500 text-violet-300' : 'border-transparent text-zinc-400 hover:text-zinc-200'"
                        class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors whitespace-nowrap inline-flex items-center gap-2">
                    {{ $t['label'] }}
                    @if($t['count'] !== null)
                        <span class="px-1.5 py-0.5 text-xs rounded-full {{ $t['id'] === 'tickets' ? 'bg-violet-500/10 text-violet-300' : 'bg-zinc-800 text-zinc-400' }}">
                            {{ $t['count'] }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Info --}}
        <div x-show="tab === 'info'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="card p-6 lg:col-span-2 space-y-4">
                <h3 class="text-base font-medium text-zinc-100">Sobre o evento</h3>
                <p class="text-sm text-zinc-300 leading-relaxed">{{ $event->description }}</p>
                @if($event->content)
                    <div class="mt-4 pt-4 border-t border-zinc-800">
                        <p class="text-sm text-zinc-300 leading-relaxed whitespace-pre-line">{{ $event->content }}</p>
                    </div>
                @endif
            </div>
            <div class="space-y-4">
                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-medium text-zinc-200">Detalhes</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center flex-shrink-0">
                                <x-icon name="calendar" class="w-3.5 h-3.5 text-violet-400" />
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-zinc-500">Data e horário</div>
                                <div class="text-zinc-100">{{ $event->starts_at->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-zinc-500 mt-0.5">até {{ $event->ends_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center flex-shrink-0">
                                <x-icon name="map-pin" class="w-3.5 h-3.5 text-violet-400" />
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-zinc-500">Local</div>
                                <div class="text-zinc-100">{{ $event->venue_name ?: 'Online' }}</div>
                                @if($event->address)
                                    <div class="text-xs text-zinc-500 mt-0.5">{{ $event->address }}</div>
                                @endif
                                <div class="text-xs text-zinc-500 mt-0.5">{{ $event->city }}/{{ $event->state }}</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center flex-shrink-0">
                                <x-icon name="users" class="w-3.5 h-3.5 text-violet-400" />
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-zinc-500">Capacidade</div>
                                <div class="text-zinc-100">{{ number_format($event->capacity ?? 0) }} pessoas</div>
                            </div>
                        </div>
                        @if($event->organization)
                            <div class="flex items-start gap-3 pt-3 border-t border-zinc-800">
                                <img src="{{ $event->organization->logoUrl() }}" alt="" class="w-8 h-8 rounded-lg">
                                <div class="flex-1">
                                    <div class="text-xs text-zinc-500">Organização</div>
                                    <div class="text-zinc-100">{{ $event->organization->name }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Tickets --}}
        <div x-show="tab === 'tickets'" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($event->ticketTypes as $tt)
                    <div class="card p-5 hover:border-violet-500/30 transition-all">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1 min-w-0">
                                <span class="badge badge-{{ $tt->type->color() }} mb-2 inline-flex">
                                    {{ $tt->type->label() }}
                                </span>
                                <h4 class="text-base font-medium text-zinc-100 truncate">{{ $tt->name }}</h4>
                            </div>
                            <div class="text-right">
                                <div class="text-xl font-semibold gradient-text">
                                    {{ $tt->formattedPrice() }}
                                </div>
                            </div>
                        </div>
                        @if($tt->description)
                            <p class="text-xs text-zinc-500 mb-3 line-clamp-2">{{ $tt->description }}</p>
                        @endif
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between text-zinc-400">
                                <span>Vendidos:</span>
                                <span class="text-zinc-100 font-medium">{{ $tt->sold }} / {{ $tt->quantity }}</span>
                            </div>
                            <div class="w-full h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-violet-500 to-violet-700 rounded-full"
                                     style="width: {{ $tt->quantity > 0 ? ($tt->sold / $tt->quantity) * 100 : 0 }}%"></div>
                            </div>
                            <div class="text-zinc-500 pt-1">
                                {{ $tt->available() }} disponíveis
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card p-8 text-center col-span-full">
                        <x-icon name="ticket" class="w-10 h-10 text-zinc-700 mx-auto mb-3" />
                        <p class="text-sm text-zinc-500">Nenhum ingresso cadastrado.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Schedule --}}
        <div x-show="tab === 'schedule'" x-cloak>
            <div class="card p-6">
                @if($event->schedules->count() > 0)
                    @php
                        $grouped = $event->schedules->groupBy(fn ($s) => $s->starts_at->format('Y-m-d'));
                    @endphp
                    <div class="space-y-6">
                        @foreach($grouped as $date => $daySchedules)
                            <div>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                        {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-zinc-500">— {{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}</div>
                                    <div class="flex-1 h-px bg-zinc-800"></div>
                                </div>
                                <div class="space-y-2">
                                    @foreach($daySchedules as $schedule)
                                        <div class="flex gap-4 p-4 rounded-lg bg-zinc-900/30 hover:bg-zinc-900/60 transition-colors">
                                            <div class="text-center min-w-16">
                                                <div class="text-base font-semibold text-zinc-100">{{ $schedule->starts_at->format('H:i') }}</div>
                                                <div class="text-xs text-zinc-500">{{ $schedule->duration() }}min</div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                    <span class="badge badge-{{ $schedule->type->color() }}">{{ $schedule->type->label() }}</span>
                                                    @if($schedule->room)
                                                        <span class="text-xs text-zinc-500">· {{ $schedule->room }}</span>
                                                    @endif
                                                </div>
                                                <h4 class="text-sm font-medium text-zinc-100">{{ $schedule->title }}</h4>
                                                @if($schedule->speaker)
                                                    <div class="flex items-center gap-2 mt-2">
                                                        <img src="{{ $schedule->speaker->avatarUrl() }}" alt="" class="w-5 h-5 rounded-full">
                                                        <span class="text-xs text-zinc-400">{{ $schedule->speaker->name }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <x-icon name="clock" class="w-10 h-10 text-zinc-700 mx-auto mb-3" />
                        <p class="text-sm text-zinc-500">Nenhuma atividade programada.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Speakers --}}
        <div x-show="tab === 'speakers'" x-cloak>
            @if($event->speakers->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($event->speakers as $speaker)
                        <div class="card p-5 text-center hover:border-violet-500/30 transition-all">
                            <img src="{{ $speaker->avatarUrl() }}" alt="{{ $speaker->name }}"
                                 class="w-20 h-20 rounded-full mx-auto mb-3 border-2 border-zinc-800 object-cover">
                            <h4 class="text-sm font-medium text-zinc-100">{{ $speaker->name }}</h4>
                            @if($speaker->role)
                                <p class="text-xs text-zinc-400 mt-0.5">{{ $speaker->role }}</p>
                            @endif
                            @if($speaker->company)
                                <p class="text-xs text-zinc-500 mt-0.5">{{ $speaker->company }}</p>
                            @endif
                            @if($speaker->is_featured)
                                <span class="badge badge-warning mt-3 inline-flex">
                                    <x-icon name="star" class="w-3 h-3" />
                                    Destaque
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card p-8 text-center">
                    <x-icon name="mic" class="w-10 h-10 text-zinc-700 mx-auto mb-3" />
                    <p class="text-sm text-zinc-500">Nenhum palestrante cadastrado.</p>
                </div>
            @endif
        </div>

        {{-- Team --}}
        <div x-show="tab === 'team'" x-cloak>
            <div class="card p-6">
                @if($event->staff->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($event->staff as $member)
                            <div class="flex items-center gap-3 p-3 rounded-lg bg-zinc-900/30 hover:bg-zinc-900/60 transition-colors">
                                <div class="w-10 h-10 rounded-full bg-violet-500/10 flex items-center justify-center text-violet-300 font-semibold flex-shrink-0">
                                    {{ mb_strtoupper(mb_substr($member->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-zinc-100 truncate">{{ $member->name }}</div>
                                    @if($member->role)
                                        <div class="text-xs text-zinc-500">{{ $member->role->name }}</div>
                                    @endif
                                </div>
                                <span class="badge badge-neutral text-xs">{{ ucfirst($member->type) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <x-icon name="user-check" class="w-10 h-10 text-zinc-700 mx-auto mb-3" />
                        <p class="text-sm text-zinc-500">Nenhum membro na equipe.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>