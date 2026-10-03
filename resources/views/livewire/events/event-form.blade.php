<div class="space-y-6">
    <x-page-header title="{{ $event ? 'Editar evento' : 'Novo evento' }}" subtitle="{{ $event ? 'Atualize as informações do evento.' : 'Preencha as informações para criar um novo evento.' }}" eyebrow="Eventos">
        <x-slot:actions>
            <a href="{{ route('dashboard.events.index') }}" class="btn btn-secondary">
                <x-icon name="chevron-left" class="w-4 h-4" />
                Voltar
            </a>
        </x-slot:actions>
    </x-page-header>

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic info --}}
                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-medium text-zinc-200 mb-4">Informações básicas</h3>

                    <div>
                        <label class="label">Nome do evento *</label>
                        <input type="text" wire:model="name" class="input" placeholder="Ex: Summit de Tecnologia 2026">
                        @error('name') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label">Descrição curta</label>
                        <textarea wire:model="description" rows="3" class="input" placeholder="Uma breve descrição do evento..."></textarea>
                        @error('description') <p class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="label">Conteúdo completo</label>
                        <textarea wire:model="content" rows="6" class="input" placeholder="Detalhes completos do evento, programação, informações adicionais..."></textarea>
                    </div>
                </div>

                {{-- Location --}}
                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-medium text-zinc-200 mb-4">Local</h3>

                    <div>
                        <label class="label">Nome do local</label>
                        <input type="text" wire:model="venue_name" class="input" placeholder="Ex: Centro de Convenções">
                    </div>

                    <div>
                        <label class="label">Endereço</label>
                        <input type="text" wire:model="address" class="input" placeholder="Rua, número">
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label class="label">Cidade</label>
                            <input type="text" wire:model="city" class="input" placeholder="São Paulo">
                        </div>
                        <div>
                            <label class="label">UF</label>
                            <input type="text" wire:model="state" maxlength="2" class="input" placeholder="SP">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">CEP</label>
                            <input type="text" wire:model="zipcode" class="input" placeholder="00000-000">
                        </div>
                        <div>
                            <label class="label">Capacidade</label>
                            <input type="number" wire:model="capacity" class="input" placeholder="500">
                        </div>
                    </div>
                </div>

                {{-- Schedule --}}
                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-medium text-zinc-200 mb-4">Data e horário</h3>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">Início *</label>
                            <input type="datetime-local" wire:model="starts_at" class="input">
                            @error('starts_at') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="label">Término *</label>
                            <input type="datetime-local" wire:model="ends_at" class="input">
                            @error('ends_at') <p class="field-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Media --}}
                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-medium text-zinc-200 mb-4">Imagens</h3>

                    <div>
                        <label class="label">Banner do evento</label>
                        <input type="file" wire:model="banner" accept="image/*" class="input">
                        @if($event?->banner_path)
                            <img src="{{ $event->bannerUrl() }}" class="mt-2 rounded-lg w-full h-32 object-cover">
                        @endif
                    </div>

                    <div>
                        <label class="label">Imagem de capa</label>
                        <input type="file" wire:model="image" accept="image/*" class="input">
                        @if($event?->image_path)
                            <img src="{{ $event->imageUrl() }}" class="mt-2 rounded-lg w-32 h-32 object-cover">
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Status --}}
                <div class="card p-6 space-y-4">
                    <h3 class="text-sm font-medium text-zinc-200 mb-4">Configurações</h3>

                    <div>
                        <label class="label">Status</label>
                        <select wire:model="status" class="input">
                            <option value="draft">Rascunho</option>
                            <option value="published">Publicado</option>
                            <option value="active">Em andamento</option>
                            <option value="finished">Finalizado</option>
                            <option value="cancelled">Cancelado</option>
                            <option value="archived">Arquivado</option>
                        </select>
                    </div>

                    <div>
                        <label class="label">Categoria</label>
                        <select wire:model="category_id" class="input">
                            <option value="">Selecione...</option>
                            @foreach($this->categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_public" class="rounded border-zinc-700 bg-zinc-900 text-violet-600 focus:ring-violet-500 focus:ring-offset-zinc-950">
                            <span class="text-sm text-zinc-300">Evento público</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="is_featured" class="rounded border-zinc-700 bg-zinc-900 text-violet-600 focus:ring-violet-500 focus:ring-offset-zinc-950">
                            <span class="text-sm text-zinc-300">Destacar evento</span>
                        </label>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="card p-6 space-y-3">
                    <button type="submit" class="btn btn-primary w-full" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <x-icon name="check" class="w-4 h-4" />
                            {{ $event ? 'Salvar alterações' : 'Criar evento' }}
                        </span>
                        <span wire:loading>
                            <x-icon name="refresh" class="w-4 h-4 animate-spin" />
                            Salvando...
                        </span>
                    </button>

                    @if($event)
                        <a href="{{ route('public.event.show', $event->slug) }}" target="_blank" class="btn btn-secondary w-full">
                            <x-icon name="arrow-up-right" class="w-4 h-4" />
                            Ver página pública
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>