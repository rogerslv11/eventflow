<div class="space-y-6">
    <x-page-header title="Palestrantes" subtitle="Gerencie os palestrantes dos seus eventos." eyebrow="Conteúdo">
        <x-slot:actions>
            <button wire:click="openForm" class="btn btn-primary">
                <x-icon name="plus" class="w-4 h-4" />
                Novo palestrante
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="toolbar">
        <div class="relative">
            <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" />
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar palestrantes..." class="input pl-9">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($speakers as $speaker)
            <div class="card p-5 text-center group hover:border-violet-500/30" wire:key="speaker-{{ $speaker->id }}">
                <img src="{{ $speaker->avatarUrl() }}" alt="{{ $speaker->name }}" class="w-20 h-20 rounded-full mx-auto mb-3 border-2 border-zinc-800">
                <h4 class="text-sm font-medium text-zinc-100">{{ $speaker->name }}</h4>
                @if($speaker->role)
                    <p class="text-xs text-zinc-400 mt-1">{{ $speaker->role }}</p>
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

                <div class="mt-4 pt-4 border-t border-zinc-800 flex items-center justify-center gap-1">
                    <button wire:click="openForm({{ $speaker->id }})" class="icon-btn" title="Editar">
                        <x-icon name="edit" class="w-4 h-4 text-zinc-400" />
                    </button>
                    <button wire:click="toggleFeatured({{ $speaker->id }})" class="icon-btn" title="Destacar">
                        <x-icon name="star" class="w-4 h-4 {{ $speaker->is_featured ? 'text-yellow-400' : 'text-zinc-400' }}" />
                    </button>
                    <button
                        x-on:click="
                            if (confirm('Excluir este palestrante?')) {
                                $wire.delete({{ $speaker->id }})
                            }
                        "
                        class="icon-btn" title="Excluir">
                        <x-icon name="trash" class="w-4 h-4 text-red-400" />
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-empty-state icon="mic" title="Nenhum palestrante cadastrado" description="Adicione palestrantes para enriquecer seus eventos.">
                    <x-slot:action>
                        <button wire:click="openForm" class="btn btn-primary">
                            <x-icon name="plus" class="w-4 h-4" />
                            Adicionar palestrante
                        </button>
                    </x-slot:action>
                </x-empty-state>
            </div>
        @endforelse
    </div>

    @if($speakers->hasPages())
        <div>{{ $speakers->links() }}</div>
    @endif

    {{-- Form Modal --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="modal-backdrop" wire:click="closeForm"></div>
            <div class="modal-panel max-w-2xl animate-modal-in max-h-[90vh] overflow-y-auto">
                <form wire:submit.prevent="save">
                    <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between sticky top-0 bg-zinc-900">
                        <h3 class="text-lg font-semibold text-zinc-100">
                            {{ $editingId ? 'Editar palestrante' : 'Novo palestrante' }}
                        </h3>
                        <button type="button" wire:click="closeForm" class="text-zinc-400 hover:text-zinc-100">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <label class="label">Nome *</label>
                                <input type="text" wire:model="name" class="input">
                                @error('name') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="label">Cargo</label>
                                <input type="text" wire:model="role" class="input">
                            </div>
                            <div>
                                <label class="label">Empresa</label>
                                <input type="text" wire:model="company" class="input">
                            </div>
                            <div>
                                <label class="label">E-mail</label>
                                <input type="email" wire:model="email" class="input">
                            </div>
                            <div>
                                <label class="label">Telefone</label>
                                <input type="text" wire:model="phone" class="input">
                            </div>
                            <div class="col-span-2">
                                <label class="label">Website</label>
                                <input type="url" wire:model="website" class="input">
                            </div>
                            <div>
                                <label class="label">LinkedIn</label>
                                <input type="url" wire:model="linkedin" class="input">
                            </div>
                            <div>
                                <label class="label">Twitter/X</label>
                                <input type="url" wire:model="twitter" class="input">
                            </div>
                            <div class="col-span-2">
                                <label class="label">Biografia</label>
                                <textarea wire:model="bio" rows="3" class="input"></textarea>
                            </div>
                            <div class="col-span-2">
                                <label class="label">Foto</label>
                                <input type="file" wire:model="avatar" accept="image/*" class="input">
                            </div>
                            <div class="col-span-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="is_featured" class="rounded border-zinc-700 bg-zinc-900 text-violet-600 focus:ring-violet-500 focus:ring-offset-zinc-950">
                                    <span class="text-sm text-zinc-300">Marcar como destaque</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-zinc-800 flex items-center justify-end gap-2 sticky bottom-0">
                        <button type="button" wire:click="closeForm" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <x-icon name="check" class="w-4 h-4" />
                            {{ $editingId ? 'Salvar' : 'Criar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>