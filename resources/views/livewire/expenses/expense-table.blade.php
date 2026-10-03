<div class="space-y-6">
    <x-page-header title="Despesas" subtitle="Controle de orçamento por evento." eyebrow="Financeiro">
        <x-slot:actions>
            <button wire:click="openForm" class="btn btn-primary"><x-icon name="plus" class="w-4 h-4" /> Nova despesa</button>
        </x-slot:actions>
    </x-page-header>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card p-4"><div class="text-xs text-zinc-500">Receita (pedidos pagos)</div><div class="text-2xl font-semibold text-emerald-400">R$ {{ number_format($stats['revenue'], 2, ',', '.') }}</div></div>
        <div class="card p-4"><div class="text-xs text-zinc-500">Pago</div><div class="text-2xl font-semibold text-zinc-100">R$ {{ number_format($stats['paid'], 2, ',', '.') }}</div></div>
        <div class="card p-4"><div class="text-xs text-zinc-500">Pendente</div><div class="text-2xl font-semibold text-amber-400">R$ {{ number_format($stats['pending'], 2, ',', '.') }}</div></div>
        <div class="card p-4"><div class="text-xs text-zinc-500">Lucro (receita − pago)</div><div class="text-2xl font-semibold {{ $stats['profit'] >= 0 ? 'text-violet-400' : 'text-red-400' }}">R$ {{ number_format($stats['profit'], 2, ',', '.') }}</div></div>
    </div>
    <div class="toolbar">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <select wire:model.live="filterEvent" class="input"><option value="">Todos os eventos</option>@foreach($events as $event)<option value="{{ $event->id }}">{{ $event->name }}</option>@endforeach</select>
            <select wire:model.live="filterStatus" class="input"><option value="">Todos os status</option>@foreach($statuses as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select>
            <div class="text-xs text-zinc-500 self-center">{{ $stats['count'] }} despesa(s)</div>
        </div>
    </div>
    <div class="card table-shell">
        <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr><th class="text-left">Despesa</th><th class="text-left">Evento</th><th class="text-right">Valor</th><th class="text-center">Status</th><th class="text-right">Ações</th></tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr class="table-row" wire:key="e-{{ $expense->id }}">
                            <td><div class="text-zinc-100 font-medium">{{ $expense->title }}</div><div class="text-xs text-zinc-500">{{ $categories[$expense->category] ?? $expense->category }} @if($expense->vendor)• {{ $expense->vendor->name }}@endif @if($expense->due_date)• vence {{ $expense->due_date->format('d/m/Y') }}@endif</div></td>
                            <td class="text-zinc-300">{{ $expense->event->name ?? '—' }}</td>
                            <td class="text-right text-zinc-100 font-medium">R$ {{ number_format($expense->amount, 2, ',', '.') }}</td>
                            <td class="text-center"><span class="badge {{ $expense->status === 'paid' ? 'badge-success' : ($expense->status === 'pending' ? 'badge-warning' : 'badge-neutral') }}">{{ $statuses[$expense->status] }}</span></td>
                            <td class="text-right whitespace-nowrap">
                                @if($expense->status === 'pending')<button wire:click="markPaid({{ $expense->id }})" class="icon-btn" title="Marcar paga"><x-icon name="check" class="w-4 h-4 text-emerald-400" /></button>@endif
                                <button wire:click="openForm({{ $expense->id }})" class="icon-btn"><x-icon name="edit" class="w-4 h-4 text-zinc-400" /></button>
                                <button x-on:click="if (confirm('Excluir?')) { $wire.delete({{ $expense->id }}) }" class="icon-btn"><x-icon name="trash" class="w-4 h-4 text-red-400" /></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-zinc-500">Nenhuma despesa registrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())<div class="px-4 py-3 divider">{{ $expenses->links() }}</div>@endif
    </div>
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="modal-backdrop" wire:click="closeForm"></div>
            <div class="modal-panel max-w-lg animate-modal-in">
                <form wire:submit.prevent="save">
                    <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-100">{{ $editingId ? 'Editar' : 'Nova' }} despesa</h3>
                        <button type="button" wire:click="closeForm" class="text-zinc-400 hover:text-zinc-100"><x-icon name="x" class="w-5 h-5" /></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div><label class="label">Evento *</label><select wire:model="event_id" class="input"><option value="">Selecione...</option>@foreach($events as $event)<option value="{{ $event->id }}">{{ $event->name }}</option>@endforeach</select>@error('event_id')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div><label class="label">Título *</label><input type="text" wire:model="title" class="input"></div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="label">Valor (R$) *</label><input type="number" step="0.01" min="0" wire:model="amount" class="input"></div>
                            <div><label class="label">Categoria</label><select wire:model="category" class="input">@foreach($categories as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div><label class="label">Status</label><select wire:model="status" class="input">@foreach($statuses as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
                            <div><label class="label">Vencimento</label><input type="date" wire:model="due_date" class="input"></div>
                            <div><label class="label">Fornecedor</label><select wire:model="vendor_id" class="input"><option value="">—</option>@foreach($vendors as $vendor)<option value="{{ $vendor->id }}">{{ $vendor->name }}</option>@endforeach</select></div>
                        </div>
                        <div><label class="label">Descrição</label><textarea wire:model="description" rows="2" class="input"></textarea></div>
                    </div>
                    <div class="px-6 py-4 border-t border-zinc-800 flex items-center justify-end gap-2">
                        <button type="button" wire:click="closeForm" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
