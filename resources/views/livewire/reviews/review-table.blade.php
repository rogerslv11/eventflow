<div class="space-y-6">
    <x-page-header title="Avaliações" subtitle="Satisfação e NPS dos participantes." eyebrow="Pós-evento" />
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card p-4"><div class="text-xs text-zinc-500">Total</div><div class="text-2xl font-semibold text-zinc-100">{{ $stats['total'] }}</div></div>
        <div class="card p-4"><div class="text-xs text-zinc-500">Nota média</div><div class="text-2xl font-semibold text-violet-400">★ {{ $stats['average'] }}</div></div>
        <div class="card p-4"><div class="text-xs text-zinc-500">Notas 5</div><div class="text-2xl font-semibold text-emerald-400">{{ $stats['five_star'] }}</div></div>
        <div class="card p-4"><div class="text-xs text-zinc-500">NPS (filtro)</div><div class="text-2xl font-semibold text-zinc-100">{{ $stats['nps'] }}</div></div>
    </div>
    <div class="toolbar">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <select wire:model.live="filterEvent" class="input md:col-span-2">
                <option value="">Todos os eventos</option>
                @foreach($events as $event)<option value="{{ $event->id }}">{{ $event->name }}</option>@endforeach
            </select>
            <select wire:model.live="filterRating" class="input">
                <option value="">Todas as notas</option>
                @for($i = 5; $i >= 1; $i--)<option value="{{ $i }}">{{ $i }} estrelas</option>@endfor
            </select>
        </div>
    </div>
    <div class="card table-shell">
        <div class="table-scroll">
            <table class="table">
                <thead>
                    <tr><th class="text-left">Participante</th><th class="text-left">Evento</th><th class="text-center">Nota</th><th class="text-left">Comentário</th><th class="text-right">Ações</th></tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr class="table-row" wire:key="r-{{ $review->id }}">
                            <td><div class="text-zinc-100 font-medium">{{ $review->name }}</div><div class="text-xs text-zinc-500">{{ $review->created_at->format('d/m/Y') }}</div></td>
                            <td class="text-zinc-300">{{ $review->event->name ?? '—' }}</td>
                            <td class="text-center text-amber-400 font-semibold">{{ str_repeat('★', $review->rating) }}<span class="text-zinc-600">{{ str_repeat('★', 5 - $review->rating) }}</span></td>
                            <td class="max-w-xs"><div class="text-zinc-200 text-sm font-medium">{{ $review->title }}</div><div class="text-xs text-zinc-400 truncate">{{ $review->comment }}</div></td>
                            <td class="text-right whitespace-nowrap">
                                <button wire:click="toggleApproval({{ $review->id }})" class="badge {{ $review->is_approved ? 'badge-success' : 'badge-neutral' }}">{{ $review->is_approved ? 'Visível' : 'Oculta' }}</button>
                                <button x-on:click="if (confirm('Excluir?')) { $wire.delete({{ $review->id }}) }" class="icon-btn"><x-icon name="trash" class="w-4 h-4 text-red-400" /></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-zinc-500">Nenhuma avaliação ainda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reviews->hasPages())<div class="px-4 py-3 divider">{{ $reviews->links() }}</div>@endif
    </div>
</div>
