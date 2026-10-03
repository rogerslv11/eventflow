<div class="space-y-6">
    <x-page-header title="Lista de Espera" subtitle="{{ $waiting }} aguardando vaga • Converta interesse em venda." eyebrow="Demanda" />
    <div class="toolbar">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <div class="relative"><x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" /><input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar nome ou e-mail..." class="input pl-9"></div>
            </div>
            <select wire:model.live="filterEvent" class="input"><option value="">Todos os eventos</option>@foreach($events as $event)<option value="{{ $event->id }}">{{ $event->name }}</option>@endforeach</select>
            <select wire:model.live="filterStatus" class="input"><option value="">Todos os status</option>@foreach($statuses as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select>
        </div>
    </div>
    <div class="card table-shell">
        <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr><th class="text-left">Interessado</th><th class="text-left">Evento</th><th class="text-center">Qtd</th><th class="text-center">Status</th><th class="text-right">Ações</th></tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        <tr class="table-row" wire:key="w-{{ $entry->id }}">
                            <td><div class="text-zinc-100 font-medium">{{ $entry->name }}</div><div class="text-xs text-zinc-500">{{ $entry->email }} @if($entry->phone)• {{ $entry->phone }}@endif</div></td>
                            <td class="text-zinc-300">{{ $entry->event->name ?? '—' }}@if($entry->ticketType)<div class="text-xs text-zinc-500">{{ $entry->ticketType->name }}</div>@endif</td>
                            <td class="text-center text-zinc-200">{{ $entry->quantity }}</td>
                            <td class="text-center"><span class="badge badge-neutral">{{ $statuses[$entry->status] ?? $entry->status }}</span></td>
                            <td class="text-right whitespace-nowrap">
                                <button wire:click="notify({{ $entry->id }})" class="icon-btn" title="Notificar"><x-icon name="bell" class="w-4 h-4 text-amber-400" /></button>
                                <button wire:click="convert({{ $entry->id }})" class="icon-btn" title="Converter"><x-icon name="check" class="w-4 h-4 text-emerald-400" /></button>
                                <button wire:click="cancelEntry({{ $entry->id }})" class="icon-btn" title="Cancelar"><x-icon name="x" class="w-4 h-4 text-zinc-400" /></button>
                                <button x-on:click="if (confirm('Excluir?')) { $wire.delete({{ $entry->id }}) }" class="icon-btn"><x-icon name="trash" class="w-4 h-4 text-red-400" /></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-zinc-500">Nenhum registro na lista de espera.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($entries->hasPages())<div class="px-4 py-3 divider">{{ $entries->links() }}</div>@endif
    </div>
</div>
