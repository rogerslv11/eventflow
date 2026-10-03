<div class="space-y-6">
    <x-page-header title="Fornecedores" subtitle="Gerencie os fornecedores dos seus eventos." eyebrow="Operação">
        <x-slot:actions>
            <button wire:click="openForm" class="btn btn-primary">
                <x-icon name="plus" class="w-4 h-4" />
                Novo fornecedor
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="toolbar">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <div class="relative">
                    <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar..." class="input pl-9">
                </div>
            </div>
            <select wire:model.live="filterCategory" class="input">
                <option value="">Todas as categorias</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card table-shell">
        <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-left">Nome</th>
                        <th class="text-left">Empresa</th>
                        <th class="text-left">Categoria</th>
                        <th class="text-left">Contato</th>
                        <th class="text-right">Custo</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                        <tr class="table-row" wire:key="v-{{ $vendor->id }}">
                            <td>
                                <div class="font-medium text-zinc-100">{{ $vendor->name }}</div>
                                @if($vendor->document)
                                    <div class="text-xs text-zinc-500">{{ $vendor->document }}</div>
                                @endif
                            </td>
                            <td class="text-zinc-300">{{ $vendor->company_name ?? '—' }}</td>
                            <td>
                                <span class="badge badge-primary">{{ $vendor->category->label() }}</span>
                            </td>
                            <td>
                                @if($vendor->email)
                                    <div class="text-xs text-zinc-300">{{ $vendor->email }}</div>
                                @endif
                                @if($vendor->phone)
                                    <div class="text-xs text-zinc-500">{{ $vendor->phone }}</div>
                                @endif
                            </td>
                            <td class="text-right text-zinc-200">
                                @if($vendor->cost)
                                    R$ {{ number_format($vendor->cost, 2, ',', '.') }}
                                @else
                                    <span class="text-zinc-500">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $vendor->status === 'active' ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $vendor->status === 'active' ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <button wire:click="openForm({{ $vendor->id }})" class="icon-btn" title="Editar">
                                    <x-icon name="edit" class="w-4 h-4 text-zinc-400" />
                                </button>
                                <button
                                    x-on:click="if (confirm('Excluir este fornecedor?')) { $wire.delete({{ $vendor->id }}) }"
                                    class="icon-btn" title="Excluir">
                                    <x-icon name="trash" class="w-4 h-4 text-red-400" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <x-icon name="truck" class="w-8 h-8 text-zinc-700" />
                                    <p class="text-sm text-zinc-500">Nenhum fornecedor cadastrado.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vendors->hasPages())
            <div class="px-4 py-3 divider">
                {{ $vendors->links() }}
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
                            {{ $editingId ? 'Editar fornecedor' : 'Novo fornecedor' }}
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
                                <label class="label">Empresa</label>
                                <input type="text" wire:model="company_name" class="input">
                            </div>
                            <div>
                                <label class="label">Categoria</label>
                                <select wire:model="category" class="input">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
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
                                <label class="label">Custo (R$)</label>
                                <input type="number" step="0.01" wire:model="cost" class="input">
                            </div>
                            <div class="col-span-2">
                                <label class="label">Endereço</label>
                                <input type="text" wire:model="address" class="input">
                            </div>
                            <div class="col-span-2">
                                <label class="label">Observações</label>
                                <textarea wire:model="notes" rows="3" class="input"></textarea>
                            </div>
                            <div class="col-span-2">
                                <label class="label">Status</label>
                                <select wire:model="status" class="input">
                                    <option value="active">Ativo</option>
                                    <option value="inactive">Inativo</option>
                                </select>
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