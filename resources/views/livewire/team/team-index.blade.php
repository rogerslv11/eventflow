<div class="space-y-6">
    <x-page-header title="Equipe" subtitle="Gerencie a equipe dos seus eventos." eyebrow="Pessoas">
        <x-slot:actions>
            <button wire:click="openForm" class="btn btn-primary">
                <x-icon name="user-plus" class="w-4 h-4" />
                Adicionar membro
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="toolbar">
        <select wire:model.live="filterEvent" class="input max-w-md">
            <option value="">Todos os eventos</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}">{{ $event->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="card table-shell">
        <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-left">Nome</th>
                        <th class="text-left">Função</th>
                        <th class="text-left">Evento</th>
                        <th class="text-left">Contato</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                        <tr class="table-row" wire:key="m-{{ $member->id }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-violet-500/10 flex items-center justify-center text-violet-300 text-xs font-semibold">
                                        {{ mb_strtoupper(mb_substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-zinc-100">{{ $member->name }}</div>
                                        @if($member->document)
                                            <div class="text-xs text-zinc-500">{{ $member->document }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($member->role)
                                    <span class="badge" style="background-color: {{ $member->role->color }}20; color: {{ $member->role->color }};">
                                        {{ $member->role->name }}
                                    </span>
                                @else
                                    <span class="text-zinc-500">—</span>
                                @endif
                            </td>
                            <td class="text-zinc-300">{{ $member->event->name ?? '—' }}</td>
                            <td>
                                @if($member->email)
                                    <div class="text-xs text-zinc-300">{{ $member->email }}</div>
                                @endif
                                @if($member->phone)
                                    <div class="text-xs text-zinc-500">{{ $member->phone }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-neutral">
                                    {{ match($member->type) {
                                        'employee' => 'Funcionário',
                                        'freelancer' => 'Freelancer',
                                        'volunteer' => 'Voluntário',
                                        default => ucfirst($member->type),
                                    } }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $member->status === 'active' ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $member->status === 'active' ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <button wire:click="openForm({{ $member->id }})" class="icon-btn">
                                    <x-icon name="edit" class="w-4 h-4 text-zinc-400" />
                                </button>
                                <button
                                    x-on:click="if (confirm('Remover da equipe?')) { $wire.delete({{ $member->id }}) }"
                                    class="icon-btn">
                                    <x-icon name="trash" class="w-4 h-4 text-red-400" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <x-icon name="user-check" class="w-8 h-8 text-zinc-700" />
                                    <p class="text-sm text-zinc-500">Nenhum membro na equipe.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($staff->hasPages())
            <div class="px-4 py-3 divider">
                {{ $staff->links() }}
            </div>
        @endif
    </div>

    {{-- Form --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="modal-backdrop" wire:click="closeForm"></div>
            <div class="modal-panel max-w-2xl animate-modal-in max-h-[90vh] overflow-y-auto">
                <form wire:submit.prevent="save">
                    <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between sticky top-0 bg-zinc-900">
                        <h3 class="text-lg font-semibold text-zinc-100">
                            {{ $editingId ? 'Editar membro' : 'Novo membro' }}
                        </h3>
                        <button type="button" wire:click="closeForm" class="text-zinc-400 hover:text-zinc-100">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="label">Evento *</label>
                            <select wire:model.live="filterEvent" class="input">
                                <option value="">Selecione...</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                            @error('filterEvent') <p class="field-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="label">Nome *</label>
                            <input type="text" wire:model="name" class="input">
                            @error('name') <p class="field-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">E-mail</label>
                                <input type="email" wire:model="email" class="input">
                            </div>
                            <div>
                                <label class="label">Telefone</label>
                                <input type="text" wire:model="phone" class="input">
                            </div>
                            <div>
                                <label class="label">Documento</label>
                                <input type="text" wire:model="document" class="input">
                            </div>
                            <div>
                                <label class="label">Função</label>
                                <select wire:model="staff_role_id" class="input" {{ $roles->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">Selecione...</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                                @if($roles->isEmpty())
                                    <p class="text-xs text-zinc-500 mt-1">Crie funções para este evento primeiro</p>
                                @endif
                            </div>
                            <div>
                                <label class="label">Tipo</label>
                                <select wire:model="type" class="input">
                                    <option value="employee">Funcionário</option>
                                    <option value="freelancer">Freelancer</option>
                                    <option value="volunteer">Voluntário</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Status</label>
                                <select wire:model="status" class="input">
                                    <option value="active">Ativo</option>
                                    <option value="inactive">Inativo</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="label">Observações</label>
                            <textarea wire:model="notes" rows="2" class="input"></textarea>
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