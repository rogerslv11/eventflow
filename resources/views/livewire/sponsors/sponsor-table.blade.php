<div class="space-y-6">
    <x-page-header title="Patrocinadores" subtitle="Gerencie os patrocinadores dos seus eventos." eyebrow="Negócios">
        <x-slot:actions>
            <button wire:click="openForm" class="btn btn-primary">
                <x-icon name="plus" class="w-4 h-4" />
                Novo patrocinador
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="toolbar">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <div class="relative">
                    <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar..." class="input pl-9">
                </div>
            </div>
            <select wire:model.live="filterEvent" class="input">
                <option value="">Todos os eventos</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="filterTier" class="input">
                <option value="">Todas as cotas</option>
                @foreach($tiers as $tier)
                    <option value="{{ $tier->value }}">{{ $tier->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tier sections --}}
    @php
        $grouped = $sponsors->groupBy('tier');
    @endphp

    @forelse($grouped as $tier => $items)
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="badge badge-{{ \App\Enums\SponsorTier::from($tier)->color() }}">
                        <x-icon name="star" class="w-3 h-3" />
                        {{ \App\Enums\SponsorTier::from($tier)->label() }}
                    </span>
                    <span class="text-xs text-zinc-500">{{ $items->count() }} patrocinadores</span>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($items as $sponsor)
                    <div class="flex flex-col items-center group" wire:key="s-{{ $sponsor->id }}">
                        <div class="w-full aspect-video bg-zinc-900 border border-zinc-800 rounded-lg flex items-center justify-center p-3 mb-2 group-hover:border-violet-500/30 transition-colors relative">
                            <img src="{{ $sponsor->logoUrl() }}" alt="{{ $sponsor->name }}" class="max-w-full max-h-full object-contain">
                            <div class="absolute inset-0 bg-zinc-950/90 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1 rounded-lg">
                                <button wire:click="openForm({{ $sponsor->id }})" class="p-1.5 bg-zinc-800 hover:bg-zinc-700 rounded">
                                    <x-icon name="edit" class="w-3.5 h-3.5 text-zinc-300" />
                                </button>
                                <button
                                    x-on:click="if (confirm('Excluir?')) { $wire.delete({{ $sponsor->id }}) }"
                                    class="p-1.5 bg-zinc-800 hover:bg-red-900/50 rounded">
                                    <x-icon name="trash" class="w-3.5 h-3.5 text-red-400" />
                                </button>
                            </div>
                        </div>
                        <div class="text-xs text-center">
                            <div class="font-medium text-zinc-100 truncate">{{ $sponsor->name }}</div>
                            @if($sponsor->contribution)
                                <div class="text-zinc-500 mt-0.5">R$ {{ number_format($sponsor->contribution, 0, ',', '.') }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <x-empty-state icon="star" title="Nenhum patrocinador cadastrado" description="Adicione patrocinadores para seus eventos.">
            <x-slot:action>
                <button wire:click="openForm" class="btn btn-primary">
                    <x-icon name="plus" class="w-4 h-4" />
                    Adicionar patrocinador
                </button>
            </x-slot:action>
        </x-empty-state>
    @endforelse

    {{-- Form --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="modal-backdrop" wire:click="closeForm"></div>
            <div class="modal-panel max-w-lg animate-modal-in max-h-[90vh] overflow-y-auto">
                <form wire:submit.prevent="save">
                    <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between sticky top-0 bg-zinc-900">
                        <h3 class="text-lg font-semibold text-zinc-100">
                            {{ $editingId ? 'Editar patrocinador' : 'Novo patrocinador' }}
                        </h3>
                        <button type="button" wire:click="closeForm" class="text-zinc-400 hover:text-zinc-100">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="label">Nome *</label>
                            <input type="text" wire:model="name" class="input">
                            @error('name') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">Evento</label>
                                <select wire:model="event_id" class="input">
                                    <option value="">Geral</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}">{{ $event->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Cota</label>
                                <select wire:model="tier" class="input">
                                    @foreach($tiers as $t)
                                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Website</label>
                                <input type="url" wire:model="website" class="input">
                            </div>
                            <div>
                                <label class="label">Contribuição (R$)</label>
                                <input type="number" step="0.01" wire:model="contribution" class="input">
                            </div>
                            <div class="col-span-2">
                                <label class="label">Descrição</label>
                                <textarea wire:model="description" rows="2" class="input"></textarea>
                            </div>
                            <div class="col-span-2">
                                <label class="label">Logo</label>
                                <input type="file" wire:model="logo" accept="image/*" class="input">
                            </div>
                            <div class="col-span-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="is_active" class="rounded border-zinc-700 bg-zinc-900 text-violet-600 focus:ring-violet-500 focus:ring-offset-zinc-950">
                                    <span class="text-sm text-zinc-300">Ativo</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-zinc-800 flex items-center justify-end gap-2 sticky bottom-0">
                        <button type="button" wire:click="closeForm" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" />
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>