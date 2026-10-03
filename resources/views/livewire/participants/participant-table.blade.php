<div class="space-y-6">
    <x-page-header title="Participantes" subtitle="Lista completa de participantes dos seus eventos." eyebrow="Público">
        <x-slot:actions>
            <button wire:click="exportCsv" class="btn btn-secondary">
                <x-icon name="download" class="w-4 h-4" />
                Exportar CSV
            </button>
        </x-slot:actions>
    </x-page-header>

    {{-- Filters --}}
    <div class="toolbar">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <div class="relative">
                    <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nome, e-mail, documento..." class="input pl-9">
                </div>
            </div>
            <select wire:model.live="filterEvent" class="input">
                <option value="">Todos os eventos</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterStatus" class="input">
                <option value="">Todos os status</option>
                <option value="confirmed">Confirmado</option>
                <option value="pending">Pendente</option>
                <option value="cancelled">Cancelado</option>
                <option value="attended">Compareceu</option>
            </select>
        </div>
        <div class="mt-3 flex items-center gap-2">
            <span class="text-xs text-zinc-500">Check-in:</span>
            <button wire:click="$set('filterCheckin', '')" class="text-xs px-2 py-1 rounded {{ $filterCheckin === '' ? 'bg-violet-500/20 text-violet-300' : 'text-zinc-400 hover:text-zinc-200' }}">Todos</button>
            <button wire:click="$set('filterCheckin', 'yes')" class="text-xs px-2 py-1 rounded {{ $filterCheckin === 'yes' ? 'bg-emerald-500/20 text-emerald-300' : 'text-zinc-400 hover:text-zinc-200' }}">Realizados</button>
            <button wire:click="$set('filterCheckin', 'no')" class="text-xs px-2 py-1 rounded {{ $filterCheckin === 'no' ? 'bg-amber-500/20 text-amber-300' : 'text-zinc-400 hover:text-zinc-200' }}">Pendentes</button>
        </div>
    </div>

    {{-- Table --}}
    <div class="card table-shell">
        <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-left">Participante</th>
                        <th class="text-left">Evento</th>
                        <th class="text-left">Ingresso</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Check-in</th>
                        <th class="text-left">Cadastro</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participants as $p)
                        <tr class="table-row" wire:key="p-{{ $p->id }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-violet-500/10 flex items-center justify-center text-violet-300 text-xs font-semibold flex-shrink-0">
                                        {{ mb_strtoupper(mb_substr($p->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-medium text-zinc-100">{{ $p->name }}</div>
                                        <div class="text-xs text-zinc-500">{{ $p->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-zinc-300">{{ $p->event->name ?? '—' }}</span>
                            </td>
                            <td>
                                @if($p->ticket?->ticketType)
                                    <span class="badge badge-primary">{{ $p->ticket->ticketType->name }}</span>
                                @else
                                    <span class="text-zinc-500">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge
                                    {{ $p->status === 'confirmed' ? 'badge-success' : '' }}
                                    {{ $p->status === 'cancelled' ? 'badge-danger' : '' }}
                                    {{ $p->status === 'pending' ? 'badge-warning' : '' }}
                                    {{ $p->status === 'attended' ? 'badge-primary' : '' }}">
                                    {{ match($p->status) {
                                        'confirmed' => 'Confirmado',
                                        'cancelled' => 'Cancelado',
                                        'pending' => 'Pendente',
                                        'attended' => 'Compareceu',
                                        default => ucfirst($p->status),
                                    } }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($p->has_checked_in)
                                    <span class="inline-flex items-center gap-1 text-emerald-400">
                                        <x-icon name="check-circle" class="w-4 h-4" />
                                        <span class="text-xs">{{ $p->checked_in_at?->format('H:i') }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-zinc-500">
                                        <x-icon name="clock" class="w-4 h-4" />
                                        <span class="text-xs">Pendente</span>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-xs text-zinc-400">{{ $p->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="text-right">
                                <div x-data="{ open: false }" class="relative inline-block">
                                    <button @click="open = !open" class="icon-btn">
                                        <x-icon name="more-horizontal" class="w-4 h-4 text-zinc-400" />
                                    </button>
                                    <div x-show="open" x-cloak x-transition @click.outside="open = false"
                                         class="absolute right-0 mt-1 w-40 bg-zinc-900 border border-zinc-800 rounded-lg p-1 shadow-xl z-10">
                                        @if($p->ticket)
                                            <a href="{{ route('public.ticket', $p->ticket->code) }}" target="_blank" class="dropdown-item text-zinc-300">
                                                <x-icon name="qr-code" class="w-3.5 h-3.5" /> Ver ingresso
                                            </a>
                                        @endif
                                        <button wire:click="cancel({{ $p->id }})" class="dropdown-item text-red-400">
                                            <x-icon name="ban" class="w-3.5 h-3.5" /> Cancelar
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <x-icon name="users" class="w-8 h-8 text-zinc-700" />
                                    <p class="text-sm text-zinc-500">Nenhum participante encontrado.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($participants->hasPages())
            <div class="px-4 py-3 divider">
                {{ $participants->links() }}
            </div>
        @endif
    </div>
</div>