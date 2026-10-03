<div class="space-y-6">
    <x-page-header title="Programação" subtitle="Gerencie a agenda dos seus eventos." eyebrow="Conteúdo">
        <x-slot:actions>
            <button wire:click="openForm" class="btn btn-primary">
                <x-icon name="plus" class="w-4 h-4" />
                Nova atividade
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

    {{-- Timeline view --}}
    <div class="card p-6">
        @if($schedules->count() > 0)
            @php
                $grouped = $schedules->groupBy(fn ($s) => $s->starts_at->format('Y-m-d'));
            @endphp

            <div class="space-y-6">
                @foreach($grouped as $date => $daySchedules)
                    <div>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="text-xs font-medium text-zinc-400 uppercase tracking-wider">
                                {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-zinc-500">— {{ \Carbon\Carbon::parse($date)->translatedFormat('l') }}</div>
                            <div class="flex-1 h-px bg-zinc-800"></div>
                        </div>

                        <div class="space-y-2">
                            @foreach($daySchedules as $schedule)
                                <div class="flex gap-4 p-4 rounded-lg bg-zinc-900/50 hover:bg-zinc-900 transition-colors group" wire:key="s-{{ $schedule->id }}">
                                    <div class="text-center min-w-16">
                                        <div class="text-lg font-semibold text-zinc-100">{{ $schedule->starts_at->format('H:i') }}</div>
                                        <div class="text-xs text-zinc-500">{{ $schedule->duration() }}min</div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="badge badge-{{ $schedule->type->color() }}">{{ $schedule->type->label() }}</span>
                                            @if($schedule->is_featured)
                                                <span class="badge badge-warning">
                                                    <x-icon name="star" class="w-3 h-3" />
                                                    Destaque
                                                </span>
                                            @endif
                                            @if($schedule->room)
                                                <span class="text-xs text-zinc-500">• {{ $schedule->room }}</span>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-medium text-zinc-100">{{ $schedule->title }}</h4>
                                        @if($schedule->speaker)
                                            <div class="flex items-center gap-2 mt-1">
                                                <img src="{{ $schedule->speaker->avatarUrl() }}" alt="" class="w-5 h-5 rounded-full">
                                                <span class="text-xs text-zinc-400">{{ $schedule->speaker->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                                        <button wire:click="openForm({{ $schedule->id }})" class="icon-btn">
                                            <x-icon name="edit" class="w-4 h-4 text-zinc-400" />
                                        </button>
                                        <button
                                            x-on:click="if (confirm('Excluir?')) { $wire.delete({{ $schedule->id }}) }"
                                            class="icon-btn">
                                            <x-icon name="trash" class="w-4 h-4 text-red-400" />
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <x-empty-state icon="clock" title="Nenhuma atividade programada" description="Adicione palestras, workshops e outras atividades.">
                <x-slot:action>
                    <button wire:click="openForm" class="btn btn-primary">
                        <x-icon name="plus" class="w-4 h-4" />
                        Adicionar atividade
                    </button>
                </x-slot:action>
            </x-empty-state>
        @endif

        @if($schedules->hasPages())
            <div class="mt-6 pt-6 border-t border-zinc-800">
                {{ $schedules->links() }}
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
                            {{ $editingId ? 'Editar atividade' : 'Nova atividade' }}
                        </h3>
                        <button type="button" wire:click="closeForm" class="text-zinc-400 hover:text-zinc-100">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="label">Evento *</label>
                            <select wire:model="filterEvent" class="input">
                                <option value="">Selecione...</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                            @error('filterEvent') <p class="field-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="label">Título *</label>
                            <input type="text" wire:model="title" class="input">
                            @error('title') <p class="field-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="label">Descrição</label>
                            <textarea wire:model="description" rows="2" class="input"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">Tipo</label>
                                <select wire:model="type" class="input">
                                    @foreach(\App\Enums\ScheduleType::cases() as $t)
                                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Sala/Local</label>
                                <input type="text" wire:model="room" class="input">
                            </div>
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
                            <div class="col-span-2">
                                <label class="label">Palestrante</label>
                                <select wire:model="speaker_id" class="input">
                                    <option value="">Selecione...</option>
                                    @foreach($speakers as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Capacidade</label>
                                <input type="number" wire:model="capacity" class="input">
                            </div>
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="is_featured" class="rounded border-zinc-700 bg-zinc-900 text-violet-600 focus:ring-violet-500 focus:ring-offset-zinc-950">
                                    <span class="text-sm text-zinc-300">Destacar</span>
                                </label>
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