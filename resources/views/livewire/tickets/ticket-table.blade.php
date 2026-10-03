<div class="space-y-6">
    <x-page-header title="Ingressos" subtitle="Gerencie os tipos de ingresso dos seus eventos." eyebrow="Vendas">
        <x-slot:actions>
            <button wire:click="openForm" class="btn btn-primary">
                <x-icon name="plus" class="w-4 h-4" />
                Novo ingresso
            </button>
        </x-slot:actions>
    </x-page-header>

    {{-- Filters --}}
    <div class="toolbar">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <div class="relative">
                    <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar..." class="input pl-9">
                </div>
            </div>
            <select wire:model.live="filterType" class="input">
                <option value="">Todos os tipos</option>
                <option value="free">Gratuito</option>
                <option value="paid">Pago</option>
                <option value="vip">VIP</option>
                <option value="premium">Premium</option>
                <option value="courtesy">Cortesia</option>
                <option value="press">Imprensa</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="card table-shell">
        <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-left">Ingresso</th>
                        <th class="text-left">Evento</th>
                        <th class="text-left">Tipo</th>
                        <th class="text-right">Preço</th>
                        <th class="text-center">Vendidos</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ticketTypes as $tt)
                        <tr class="table-row" wire:key="tt-{{ $tt->id }}">
                            <td>
                                <div class="font-medium text-zinc-100">{{ $tt->name }}</div>
                                @if($tt->description)
                                    <div class="text-xs text-zinc-500 mt-0.5 truncate max-w-xs">{{ $tt->description }}</div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('dashboard.events.show', $tt->event_id) }}" class="text-violet-400 hover:text-violet-300">
                                    {{ $tt->event->name ?? '—' }}
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-{{ $tt->type->color() }}">{{ $tt->type->label() }}</span>
                            </td>
                            <td class="text-right font-medium text-zinc-200">
                                {{ $tt->formattedPrice() }}
                            </td>
                            <td class="text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-zinc-200 font-medium">{{ $tt->sold }}/{{ $tt->quantity }}</span>
                                    <div class="w-16 h-1 mt-1 bg-zinc-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-violet-500 to-violet-700" style="width: {{ $tt->quantity > 0 ? ($tt->sold / $tt->quantity) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button wire:click="toggleActive({{ $tt->id }})" class="badge {{ $tt->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $tt->is_active ? 'Ativo' : 'Inativo' }}
                                </button>
                            </td>
                            <td class="text-right">
                                <div x-data="{ open: false }" class="relative inline-block" @click.outside="open = false">
                                    <button @click="open = !open" class="icon-btn">
                                        <x-icon name="more-horizontal" class="w-4 h-4 text-zinc-400" />
                                    </button>
                                    <div x-show="open" x-cloak x-transition class="absolute right-0 mt-1 w-40 bg-zinc-900 border border-zinc-800 rounded-lg p-1 shadow-xl z-10">
                                        <button wire:click="openForm({{ $tt->id }})" @click="open = false" class="dropdown-item text-zinc-300">
                                            <x-icon name="edit" class="w-3.5 h-3.5" /> Editar
                                        </button>
                                        <button
                                            x-on:click="
                                                open = false;
                                                if (confirm('Excluir este ingresso?')) {
                                                    $wire.delete({{ $tt->id }})
                                                }
                                            "
                                            class="dropdown-item text-red-400">
                                            <x-icon name="trash" class="w-3.5 h-3.5" /> Excluir
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <x-icon name="ticket" class="w-8 h-8 text-zinc-700" />
                                    <p class="text-sm text-zinc-500">Nenhum ingresso cadastrado.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ticketTypes->hasPages())
            <div class="px-4 py-3 divider">
                {{ $ticketTypes->links() }}
            </div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="modal-backdrop" wire:click="closeForm"></div>
            <div class="modal-panel max-w-2xl animate-modal-in max-h-[90vh] overflow-y-auto">
                <form wire:submit.prevent="save">
                    <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between sticky top-0 bg-zinc-900">
                        <h3 class="text-lg font-semibold text-zinc-100">
                            {{ $editingId ? 'Editar ingresso' : 'Novo ingresso' }}
                        </h3>
                        <button type="button" wire:click="closeForm" class="text-zinc-400 hover:text-zinc-100">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="label">Evento *</label>
                            <select wire:model="event_id" class="input">
                                <option value="">Selecione...</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                            @error('event_id') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">Nome *</label>
                                <input type="text" wire:model="name" class="input" placeholder="VIP, Standard...">
                                @error('name') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="label">Tipo *</label>
                                <select wire:model="type" class="input">
                                    <option value="paid">Pago</option>
                                    <option value="free">Gratuito</option>
                                    <option value="vip">VIP</option>
                                    <option value="premium">Premium</option>
                                    <option value="courtesy">Cortesia</option>
                                    <option value="press">Imprensa</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="label">Descrição</label>
                            <textarea wire:model="description" rows="2" class="input"></textarea>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="label">Preço (R$) *</label>
                                <input type="number" step="0.01" wire:model="price" class="input">
                                @error('price') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="label">Quantidade *</label>
                                <input type="number" wire:model="quantity" class="input">
                                @error('quantity') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="label">Ativo</label>
                                <select wire:model="is_active" class="input">
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">Mín./compra</label>
                                <input type="number" wire:model="min_per_order" class="input">
                            </div>
                            <div>
                                <label class="label">Máx./compra</label>
                                <input type="number" wire:model="max_per_order" class="input">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">Início das vendas</label>
                                <input type="datetime-local" wire:model="sales_start_at" class="input">
                            </div>
                            <div>
                                <label class="label">Fim das vendas</label>
                                <input type="datetime-local" wire:model="sales_end_at" class="input">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-zinc-800 flex items-center justify-end gap-2 sticky bottom-0">
                        <button type="button" wire:click="closeForm" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <x-icon name="check" class="w-4 h-4" />
                                {{ $editingId ? 'Salvar' : 'Criar' }}
                            </span>
                            <span wire:loading>
                                <x-icon name="refresh" class="w-4 h-4 animate-spin" />
                                Salvando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>