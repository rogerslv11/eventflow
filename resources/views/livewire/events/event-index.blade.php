<div class="space-y-6">
    {{-- Header --}}
    <x-page-header title="Eventos" subtitle="Gerencie todos os seus eventos em um só lugar." eyebrow="Operação">
        <x-slot:actions>
            <a href="{{ route('dashboard.events.create') }}" class="btn btn-primary">
                <x-icon name="plus" class="w-4 h-4" />
                Novo evento
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Filters --}}
    <div class="toolbar">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <div class="relative">
                    <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" />
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar eventos..."
                        class="input pl-9">
                </div>
            </div>

            <select wire:model.live="status" class="input">
                <option value="">Todos os status</option>
                <option value="draft">Rascunho</option>
                <option value="published">Publicado</option>
                <option value="active">Em andamento</option>
                <option value="finished">Finalizado</option>
                <option value="cancelled">Cancelado</option>
                <option value="archived">Arquivado</option>
            </select>

            <select wire:model.live="category" class="input">
                <option value="">Todas as categorias</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Grid --}}
    @if($events->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($events as $event)
                <div class="card overflow-hidden card-hover group" wire:key="event-{{ $event->id }}">
                    {{-- Banner --}}
                    <div class="relative h-40 bg-zinc-900 overflow-hidden">
                        <img src="{{ $event->bannerUrl() }}" alt="{{ $event->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/30 to-transparent"></div>

                        {{-- Status badge --}}
                        <div class="absolute top-3 left-3">
                            <span class="badge badge-{{ event_status_badge($event->status->value) }}">
                                <x-icon :name="event_status_icon($event->status->value)" class="w-3 h-3" />
                                {{ $event->status->label() }}
                            </span>
                        </div>

                        @if($event->is_featured)
                            <div class="absolute top-3 right-3">
                                <span class="badge badge-warning">
                                    <x-icon name="star" class="w-3 h-3" />
                                    Destaque
                                </span>
                            </div>
                        @endif

                        {{-- Date overlay --}}
                        <div class="absolute bottom-3 left-3 right-3">
                            <div class="flex items-center gap-2 text-xs text-zinc-300">
                                <x-icon name="calendar" class="w-3.5 h-3.5" />
                                {{ $event->starts_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-4">
                        <h3 class="font-medium text-zinc-100 truncate group-hover:text-violet-300 transition-colors">
                            {{ $event->name }}
                        </h3>
                        <p class="text-xs text-zinc-500 mt-1 line-clamp-2">
                            {{ $event->description }}
                        </p>

                        <div class="mt-3 flex items-center gap-3 text-xs text-zinc-400">
                            <div class="flex items-center gap-1">
                                <x-icon name="map-pin" class="w-3 h-3" />
                                {{ $event->city ?? 'Online' }}
                            </div>
                            <div class="flex items-center gap-1">
                                <x-icon name="users" class="w-3 h-3" />
                                {{ $event->tickets_count }}
                            </div>
                            <div class="flex items-center gap-1">
                                <x-icon name="check-circle" class="w-3 h-3" />
                                {{ $event->check_ins_count }}
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-4 pt-4 border-t border-zinc-800 flex items-center justify-between">
                            <a href="{{ route('dashboard.events.show', $event) }}" class="text-xs text-violet-400 hover:text-violet-300">
                                Ver detalhes →
                            </a>

                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="icon-btn transition-colors">
                                    <x-icon name="more-horizontal" class="w-4 h-4 text-zinc-400" />
                                </button>
                                <div x-show="open" x-cloak x-transition @click.outside="open = false"
                                     class="dropdown-panel absolute right-0 bottom-full mb-1 w-48 shadow-xl z-10">
                                    <a href="{{ route('dashboard.events.show', $event) }}" class="dropdown-item text-zinc-300">
                                        <x-icon name="eye" class="w-3.5 h-3.5" /> Ver detalhes
                                    </a>
                                    <a href="{{ route('dashboard.events.edit', $event) }}" class="dropdown-item text-zinc-300">
                                        <x-icon name="edit" class="w-3.5 h-3.5" /> Editar
                                    </a>
                                    <a href="{{ route('public.event.show', $event->slug) }}" target="_blank" class="dropdown-item text-zinc-300">
                                        <x-icon name="arrow-up-right" class="w-3.5 h-3.5" /> Página pública
                                    </a>
                                    @if($event->status === \App\Enums\EventStatus::Draft)
                                        <button wire:click="publish({{ $event->id }})" @click="open = false" class="dropdown-item text-zinc-300">
                                            <x-icon name="check-circle" class="w-3.5 h-3.5" /> Publicar
                                        </button>
                                    @endif
                                    @if($event->status === \App\Enums\EventStatus::Published)
                                        <button wire:click="unpublish({{ $event->id }})" @click="open = false" class="dropdown-item text-zinc-300">
                                            <x-icon name="file-text" class="w-3.5 h-3.5" /> Despublicar
                                        </button>
                                    @endif
                                    <button wire:click="duplicate({{ $event->id }})" @click="open = false" class="dropdown-item text-zinc-300">
                                        <x-icon name="copy" class="w-3.5 h-3.5" /> Duplicar
                                    </button>
                                    @if($event->status !== \App\Enums\EventStatus::Cancelled)
                                        <button wire:click="cancel({{ $event->id }})" @click="open = false" class="dropdown-item text-amber-400">
                                            <x-icon name="x-circle" class="w-3.5 h-3.5" /> Cancelar
                                        </button>
                                    @endif
                                    <button
                                        x-on:click="
                                            open = false;
                                            if (confirm('Tem certeza que deseja excluir este evento?')) {
                                                $wire.delete({{ $event->id }})
                                            }
                                        "
                                        class="dropdown-item text-red-400">
                                        <x-icon name="trash" class="w-3.5 h-3.5" /> Excluir
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @else
        <x-empty-state icon="calendar" title="Nenhum evento encontrado" description="Comece criando seu primeiro evento.">
            <x-slot:action>
                <a href="{{ route('dashboard.events.create') }}" class="btn btn-primary">
                    <x-icon name="plus" class="w-4 h-4" />
                    Criar evento
                </a>
            </x-slot:action>
        </x-empty-state>
    @endif
</div>