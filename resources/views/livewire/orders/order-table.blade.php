<div class="space-y-6">
    <x-page-header title="Pedidos" subtitle="Gerencie vendas, reembolsos e cancelamentos." eyebrow="Financeiro" />

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card p-4"><div class="text-xs text-zinc-500">Total de pedidos</div><div class="text-2xl font-semibold text-zinc-100">{{ $stats['total'] }}</div></div>
        <div class="card p-4"><div class="text-xs text-zinc-500">Receita (pagos)</div><div class="text-2xl font-semibold text-emerald-400">R$ {{ number_format($stats['revenue'], 2, ',', '.') }}</div></div>
        <div class="card p-4"><div class="text-xs text-zinc-500">Pendentes</div><div class="text-2xl font-semibold text-amber-400">{{ $stats['pending'] }}</div></div>
        <div class="card p-4"><div class="text-xs text-zinc-500">Reembolsados</div><div class="text-2xl font-semibold text-zinc-300">{{ $stats['refunded'] }}</div></div>
    </div>

    <div class="toolbar">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <div class="relative">
                    <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por código, nome ou e-mail..." class="input pl-9">
                </div>
            </div>
            <select wire:model.live="status" class="input">
                <option value="">Todos os status</option>
                @foreach($statuses as $s)
                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                @endforeach
            </select>
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
                        <th class="text-left">Pedido</th>
                        <th class="text-left">Cliente</th>
                        <th class="text-left">Evento</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="table-row" wire:key="o-{{ $order->id }}">
                            <td><code class="whitespace-nowrap px-2 py-1 bg-violet-500/10 text-violet-300 rounded font-mono text-xs">{{ $order->code }}</code><div class="text-xs text-zinc-500 mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</div></td>
                            <td><div class="text-zinc-100 font-medium">{{ $order->customer_name }}</div><div class="text-xs text-zinc-500">{{ $order->customer_email }}</div></td>
                            <td class="text-zinc-300">{{ $order->event->name ?? '—' }}</td>
                            <td class="text-right text-zinc-100 font-medium">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                            <td class="text-center"><span class="badge badge-{{ $order->status->color() }}">{{ $order->status->label() }}</span></td>
                            <td class="text-right whitespace-nowrap">
                                <button wire:click="showDetail({{ $order->id }})" class="icon-btn"><x-icon name="eye" class="w-4 h-4 text-zinc-400" /></button>
                                @if($order->status === \App\Enums\OrderStatus::Pending)
                                    <button wire:click="markPaid({{ $order->id }})" class="icon-btn" title="Marcar como pago"><x-icon name="check" class="w-4 h-4 text-emerald-400" /></button>
                                @endif
                                @if(in_array($order->status, [\App\Enums\OrderStatus::Pending, \App\Enums\OrderStatus::Paid]))
                                    <button wire:click="refund({{ $order->id }})" class="icon-btn" title="Reembolsar"><x-icon name="refresh" class="w-4 h-4 text-amber-400" /></button>
                                    <button x-on:click="if (confirm('Cancelar este pedido?')) { $wire.cancel({{ $order->id }}) }" class="icon-btn" title="Cancelar"><x-icon name="x" class="w-4 h-4 text-red-400" /></button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-zinc-500">Nenhum pedido encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-4 py-3 divider">{{ $orders->links() }}</div>
        @endif
    </div>

    @if($detail)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="modal-backdrop" wire:click="closeDetail"></div>
            <div class="modal-panel max-w-lg animate-modal-in">
                <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-zinc-100 font-mono">{{ $detail->code }}</h3>
                    <button wire:click="closeDetail" class="text-zinc-400 hover:text-zinc-100"><x-icon name="x" class="w-5 h-5" /></button>
                </div>
                <div class="p-6 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-zinc-500">Cliente</span><span class="text-zinc-100">{{ $detail->customer_name }} — {{ $detail->customer_email }}</span></div>
                    <div class="flex justify-between"><span class="text-zinc-500">Evento</span><span class="text-zinc-100">{{ $detail->event->name ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-zinc-500">Subtotal</span><span class="text-zinc-100">R$ {{ number_format($detail->subtotal, 2, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-zinc-500">Desconto</span><span class="text-zinc-100">R$ {{ number_format($detail->discount, 2, ',', '.') }}</span></div>
                    <div class="flex justify-between font-semibold"><span class="text-zinc-300">Total</span><span class="text-zinc-100">R$ {{ number_format($detail->total, 2, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-zinc-500">Pagamento</span><span class="text-zinc-100">{{ $detail->payment_method ?? '—' }} • {{ $detail->status->label() }}</span></div>
                    <div class="pt-2 border-t border-zinc-800">
                        <div class="text-zinc-500 mb-2">Itens ({{ $detail->items->count() }})</div>
                        @foreach($detail->items as $item)
                            <div class="flex justify-between text-xs py-1"><span class="text-zinc-300">{{ $item->ticket_type_name ?? 'Ingresso' }} × {{ $item->quantity }}</span><span class="text-zinc-400">R$ {{ number_format($item->total ?? $item->price ?? 0, 2, ',', '.') }}</span></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
