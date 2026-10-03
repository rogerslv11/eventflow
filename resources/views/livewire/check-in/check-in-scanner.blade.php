<div class="space-y-6">
    <x-page-header title="Check-in" subtitle="Valide ingressos via QR Code em tempo real." eyebrow="Operação">
        <x-slot:actions>
            <select wire:model.live="eventId" class="input !w-auto min-w-52">
            <option value="">Todos os eventos</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}">{{ $event->name }}</option>
            @endforeach
        </select>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Scanner --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Input --}}
            <div class="card p-6">
                <label class="label">Código do ingresso / QR Code</label>
                <form wire:submit.prevent="checkIn" class="flex gap-2">
                    <input
                        type="text"
                        wire:model="code"
                        wire:keydown.enter.prevent="checkIn"
                        placeholder="Digite ou cole o código do ingresso"
                        class="input flex-1"
                        autofocus>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <x-icon name="check" class="w-4 h-4" />
                        Validar
                    </button>
                </form>
                <p class="text-xs text-zinc-500 mt-2">
                    Você pode usar um leitor de QR Code USB para maior velocidade.
                </p>
            </div>

            {{-- Last result --}}
            @if($lastResult)
                <div class="card p-6 animate-slide-up
                    {{ $lastResult['status'] === 'success' ? 'border-emerald-500/50 bg-emerald-500/5' : '' }}
                    {{ $lastResult['status'] === 'duplicate' ? 'border-amber-500/50 bg-amber-500/5' : '' }}
                    {{ in_array($lastResult['status'], ['invalid', 'cancelled']) ? 'border-red-500/50 bg-red-500/5' : '' }}
                ">
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0
                            {{ $lastResult['status'] === 'success' ? 'bg-emerald-500/20 text-emerald-400' : '' }}
                            {{ $lastResult['status'] === 'duplicate' ? 'bg-amber-500/20 text-amber-400' : '' }}
                            {{ in_array($lastResult['status'], ['invalid', 'cancelled']) ? 'bg-red-500/20 text-red-400' : '' }}
                        ">
                            @if($lastResult['status'] === 'success')
                                <x-icon name="check-circle" class="w-8 h-8" />
                            @elseif($lastResult['status'] === 'duplicate')
                                <x-icon name="alert-triangle" class="w-8 h-8" />
                            @else
                                <x-icon name="x-circle" class="w-8 h-8" />
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-zinc-100">
                                @switch($lastResult['status'])
                                    @case('success') Check-in confirmado
                                    @case('duplicate') Check-in já realizado
                                    @case('invalid') Ingresso inválido
                                    @case('cancelled') Ingresso cancelado
                                    @default Resultado
                                @endswitch
                            </h3>
                            <p class="text-sm text-zinc-400 mt-1">{{ $lastResult['message'] }}</p>

                            @if($lastResult['ticket'])
                                <div class="mt-4 p-4 bg-zinc-900 rounded-lg space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-zinc-500">Nome:</span>
                                        <span class="text-zinc-200 font-medium">{{ $lastResult['ticket']['name'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-zinc-500">Tipo:</span>
                                        <span class="text-zinc-200">{{ $lastResult['ticket']['type'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-zinc-500">Evento:</span>
                                        <span class="text-zinc-200 truncate ml-2">{{ $lastResult['ticket']['event'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-zinc-500">Código:</span>
                                        <code class="text-violet-300">{{ $lastResult['ticket']['code'] }}</code>
                                    </div>
                                </div>
                            @endif

                            @if($lastResult['status'] === 'success')
                                <div class="mt-3 text-xs text-zinc-500">
                                    Entrada registrada às <span class="text-zinc-300 font-medium">{{ $lastResult['time'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <x-empty-state icon="scan" title="Pronto para validar" description="Digite ou escaneie o código do ingresso para iniciar." />
            @endif

            {{-- Hour chart --}}
            @if($this->eventId && array_sum($byHour) > 0)
                <div class="card p-6">
                    <h3 class="text-sm font-medium text-zinc-200 mb-4">Check-ins por horário (hoje)</h3>
                    <div class="flex items-end justify-between gap-1 h-32">
                        @foreach($byHour as $hour => $count)
                            @php
                                $maxCount = max($byHour ?: [1]);
                                $height = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                            @endphp
                            <div class="flex-1 flex flex-col items-center gap-1 group">
                                <div class="text-xs text-zinc-500 opacity-0 group-hover:opacity-100 transition">
                                    {{ $count }}
                                </div>
                                <div class="w-full bg-gradient-to-t from-violet-600 to-violet-400 rounded-t transition-all hover:from-violet-500 hover:to-violet-300"
                                     style="height: {{ max($height, 4) }}%"></div>
                                <div class="text-[10px] text-zinc-600">{{ sprintf('%02d', $hour) }}h</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Stats sidebar --}}
        <div class="space-y-4">
            <div class="card p-5">
                <div class="text-xs text-zinc-400 mb-1">Total esperado</div>
                <div class="text-3xl font-semibold text-zinc-100">{{ $stats['total'] ?? 0 }}</div>
            </div>
            <div class="card p-5">
                <div class="text-xs text-zinc-400 mb-1">Check-ins realizados</div>
                <div class="text-3xl font-semibold text-emerald-400">{{ $stats['checked_in'] ?? 0 }}</div>
            </div>
            <div class="card p-5">
                <div class="text-xs text-zinc-400 mb-1">Pendentes</div>
                <div class="text-3xl font-semibold text-amber-400">{{ $stats['pending'] ?? 0 }}</div>
            </div>
            <div class="card p-5">
                <div class="text-xs text-zinc-400 mb-1">Taxa de presença</div>
                <div class="text-3xl font-semibold gradient-text">{{ $stats['rate'] ?? 0 }}%</div>
                @if(($stats['total'] ?? 0) > 0)
                    <div class="mt-3 w-full h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-violet-500 to-violet-700" style="width: {{ $stats['rate'] ?? 0 }}%"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent check-ins --}}
    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-zinc-800 flex items-center justify-between">
            <h3 class="text-sm font-medium text-zinc-200">Últimos check-ins</h3>
            <span class="badge badge-success">{{ count($recentCheckIns) }}</span>
        </div>
        <div class="divide-y divide-zinc-800 max-h-96 overflow-y-auto">
            @forelse($recentCheckIns as $checkIn)
                <div class="px-5 py-3 flex items-center gap-3 hover:bg-zinc-900/30">
                    <div class="w-9 h-9 rounded-full bg-emerald-500/10 flex items-center justify-center">
                        <x-icon name="check-circle" class="w-4 h-4 text-emerald-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-zinc-100">{{ $checkIn['ticket']['participant_name'] ?? '—' }}</div>
                        <div class="text-xs text-zinc-500">{{ $checkIn['event']['name'] ?? '' }}</div>
                    </div>
                    <div class="text-xs text-zinc-500">
                        {{ \Carbon\Carbon::parse($checkIn['checked_in_at'])->format('H:i') }}
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center text-sm text-zinc-500">
                    Nenhum check-in realizado ainda
                </div>
            @endforelse
        </div>
    </div>
</div>