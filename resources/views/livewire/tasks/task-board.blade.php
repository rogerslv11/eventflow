<div class="space-y-6">
    <x-page-header title="Tarefas" subtitle="{{ $done }}/{{ $total }} concluídas ({{ $progress }}%)" eyebrow="Operação">
        <x-slot:actions>
            <button wire:click="openForm" class="btn btn-primary"><x-icon name="plus" class="w-4 h-4" /> Nova tarefa</button>
        </x-slot:actions>
    </x-page-header>
    <div class="w-full bg-zinc-800 rounded-full h-2"><div class="bg-violet-500 h-2 rounded-full transition-all" style="width: {{ $progress }}%"></div></div>
    <div class="toolbar">
        <select wire:model.live="filterEvent" class="input max-w-sm">
            <option value="">Todos os eventos</option>
            @foreach($events as $event)<option value="{{ $event->id }}">{{ $event->name }}</option>@endforeach
        </select>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        @foreach(['todo' => 'A fazer', 'doing' => 'Em andamento', 'done' => 'Concluídas'] as $status => $label)
            <div class="card p-4">
                <h3 class="text-sm font-semibold text-zinc-200 uppercase tracking-wider mb-3">{{ $label }} ({{ ($grouped[$status] ?? collect())->count() }})</h3>
                <div class="space-y-2">
                    @forelse($grouped[$status] ?? [] as $task)
                        <div class="p-3 rounded-lg border border-zinc-800 bg-zinc-900/50" wire:key="t-{{ $task->id }}">
                            <div class="flex items-start justify-between gap-2">
                                <div class="font-medium text-zinc-100 text-sm">{{ $task->title }}</div>
                                <span class="badge {{ $task->priority === 'urgent' ? 'badge-danger' : ($task->priority === 'high' ? 'badge-warning' : 'badge-neutral') }} shrink-0">{{ \App\Models\EventTask::priorities()[$task->priority] }}</span>
                            </div>
                            <div class="text-xs text-zinc-500 mt-1">{{ $task->event->name ?? '' }} @if($task->assigned_to)• {{ $task->assigned_to }}@endif @if($task->due_date)• {{ $task->due_date->format('d/m/Y') }}@endif</div>
                            @if($task->description)<p class="text-xs text-zinc-400 mt-1">{{ \Illuminate\Support\Str::limit($task->description, 100) }}</p>@endif
                            <div class="flex items-center gap-1 mt-2">
                                <button wire:click="advance({{ $task->id }})" class="icon-btn" title="Avançar"><x-icon name="check" class="w-4 h-4 text-emerald-400" /></button>
                                <button wire:click="openForm({{ $task->id }})" class="icon-btn"><x-icon name="edit" class="w-4 h-4 text-zinc-400" /></button>
                                <button x-on:click="if (confirm('Excluir?')) { $wire.delete({{ $task->id }}) }" class="icon-btn"><x-icon name="trash" class="w-4 h-4 text-red-400" /></button>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-zinc-600 py-4 text-center">Nada aqui.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="modal-backdrop" wire:click="closeForm"></div>
            <div class="modal-panel max-w-lg animate-modal-in">
                <form wire:submit.prevent="save">
                    <div class="px-6 py-4 border-b border-zinc-800 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-zinc-100">{{ $editingId ? 'Editar' : 'Nova' }} tarefa</h3>
                        <button type="button" wire:click="closeForm" class="text-zinc-400 hover:text-zinc-100"><x-icon name="x" class="w-5 h-5" /></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div><label class="label">Evento *</label><select wire:model="event_id" class="input"><option value="">Selecione...</option>@foreach($events as $event)<option value="{{ $event->id }}">{{ $event->name }}</option>@endforeach</select>@error('event_id')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div><label class="label">Título *</label><input type="text" wire:model="title" class="input">@error('title')<p class="field-error">{{ $message }}</p>@enderror</div>
                        <div><label class="label">Descrição</label><textarea wire:model="description" rows="2" class="input"></textarea></div>
                        <div class="grid grid-cols-3 gap-3">
                            <div><label class="label">Prioridade</label><select wire:model="priority" class="input"><option value="low">Baixa</option><option value="medium">Média</option><option value="high">Alta</option><option value="urgent">Urgente</option></select></div>
                            <div><label class="label">Prazo</label><input type="date" wire:model="due_date" class="input"></div>
                            <div><label class="label">Responsável</label><input type="text" wire:model="assigned_to" class="input"></div>
                        </div>
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
