<div class="space-y-6">
    <x-page-header title="Cupons" subtitle="Crie e gerencie cupons de desconto." eyebrow="Vendas">
        <x-slot:actions>
            <button wire:click="openForm" class="btn btn-primary">
                <x-icon name="plus" class="w-4 h-4" />
                Novo cupom
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="toolbar">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <div class="relative">
                    <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por código ou nome..." class="input pl-9">
                </div>
            </div>
            <select wire:model.live="filterEvent" class="input">
                <option value="">Todos os eventos</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card table-shell">
        <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-left">Código</th>
                        <th class="text-left">Desconto</th>
                        <th class="text-left">Evento</th>
                        <th class="text-center">Usos</th>
                        <th class="text-center">Validade</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr class="table-row" wire:key="c-{{ $coupon->id }}">
                            <td>
                                <code class="px-2 py-1 bg-violet-500/10 text-violet-300 rounded font-mono text-sm">{{ $coupon->code }}</code>
                                <div class="text-xs text-zinc-500 mt-1">{{ $coupon->name }}</div>
                            </td>
                            <td>
                                @if($coupon->type === \App\Enums\CouponType::Percentage)
                                    <span class="text-zinc-200 font-medium">{{ number_format($coupon->value, 0) }}%</span>
                                    <div class="text-xs text-zinc-500">desconto</div>
                                @else
                                    <span class="text-zinc-200 font-medium">R$ {{ number_format($coupon->value, 2, ',', '.') }}</span>
                                    <div class="text-xs text-zinc-500">valor fixo</div>
                                @endif
                            </td>
                            <td class="text-zinc-300">{{ $coupon->event->name ?? 'Geral' }}</td>
                            <td class="text-center">
                                <span class="text-zinc-200 font-medium">{{ $coupon->usage_count }}</span>
                                @if($coupon->usage_limit)
                                    <span class="text-zinc-500">/ {{ $coupon->usage_limit }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="text-xs text-zinc-400">{{ $coupon->starts_at?->format('d/m/Y') ?? '—' }}</div>
                                <div class="text-xs text-zinc-500">até {{ $coupon->ends_at?->format('d/m/Y') ?? '—' }}</div>
                            </td>
                            <td class="text-center">
                                <button wire:click="toggleActive({{ $coupon->id }})" class="badge {{ $coupon->is_active ? 'badge-success' : 'badge-neutral' }}">
                                    {{ $coupon->is_active ? 'Ativo' : 'Inativo' }}
                                </button>
                            </td>
                            <td class="text-right">
                                <button wire:click="openForm({{ $coupon->id }})" class="icon-btn">
                                    <x-icon name="edit" class="w-4 h-4 text-zinc-400" />
                                </button>
                                <button
                                    x-on:click="if (confirm('Excluir este cupom?')) { $wire.delete({{ $coupon->id }}) }"
                                    class="icon-btn">
                                    <x-icon name="trash" class="w-4 h-4 text-red-400" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <x-icon name="tag" class="w-8 h-8 text-zinc-700" />
                                    <p class="text-sm text-zinc-500">Nenhum cupom cadastrado.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="px-4 py-3 divider">
                {{ $coupons->links() }}
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
                            {{ $editingId ? 'Editar cupom' : 'Novo cupom' }}
                        </h3>
                        <button type="button" wire:click="closeForm" class="text-zinc-400 hover:text-zinc-100">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <label class="label">Código *</label>
                                <input type="text" wire:model="code" class="input uppercase">
                                @error('code') <p class="field-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex items-end">
                                <button type="button" wire:click="generateCode" class="btn btn-secondary w-full">
                                    <x-icon name="refresh" class="w-4 h-4" />
                                    Gerar
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="label">Nome *</label>
                            <input type="text" wire:model="name" class="input">
                            @error('name') <p class="field-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="label">Descrição</label>
                            <textarea wire:model="description" rows="2" class="input"></textarea>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="label">Tipo</label>
                                <select wire:model="type" class="input">
                                    <option value="percentage">Percentual</option>
                                    <option value="fixed">Valor fixo</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Valor *</label>
                                <input type="number" step="0.01" wire:model="value" class="input">
                            </div>
                            <div>
                                <label class="label">Evento</label>
                                <select wire:model="event_id" class="input">
                                    <option value="">Todos</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}">{{ $event->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="label">Limite de usos</label>
                                <input type="number" wire:model="usage_limit" class="input">
                            </div>
                            <div>
                                <label class="label">Por usuário</label>
                                <input type="number" wire:model="usage_limit_per_user" class="input">
                            </div>
                            <div>
                                <label class="label">Compra mínima</label>
                                <input type="number" step="0.01" wire:model="min_purchase" class="input">
                            </div>
                        </div>

                        <div>
                            <label class="label">Desconto máximo (R$)</label>
                                <input type="number" step="0.01" wire:model="max_discount" class="input">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">Início</label>
                                <input type="datetime-local" wire:model="starts_at" class="input">
                            </div>
                            <div>
                                <label class="label">Término</label>
                                <input type="datetime-local" wire:model="ends_at" class="input">
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="rounded border-zinc-700 bg-zinc-900 text-violet-600 focus:ring-violet-500 focus:ring-offset-zinc-950">
                                <span class="text-sm text-zinc-300">Cupom ativo</span>
                            </label>
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