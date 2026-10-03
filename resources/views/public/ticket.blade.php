<x-layouts.public>
    <header class="sticky top-0 z-40 border-b border-zinc-800/80 bg-zinc-950/85 backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-4xl items-center justify-between px-4 lg:px-6">
            <a href="{{ route('public.events.index') }}" class="flex items-center gap-2.5">
                <div class="relative flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-violet-400 via-violet-600 to-violet-800 shadow-[0_4px_16px_-4px_rgb(124_58_237/0.7)]">
                    <x-icon name="sparkles" class="h-4 w-4 text-white" />
                    <div class="absolute inset-0 rounded-xl ring-1 ring-inset ring-white/20"></div>
                </div>
                <span class="text-[0.9375rem] font-semibold tracking-tight text-zinc-50">EventFlow</span>
            </a>
            <button onclick="window.print()" class="btn btn-secondary btn-sm print:hidden">
                <x-icon name="download" class="w-4 h-4" />
                Imprimir
            </button>
        </div>
    </header>

    <main class="max-w-md mx-auto px-4 py-8">
        <div class="card overflow-hidden">
            {{-- Top gradient --}}
            <div class="h-2 bg-gradient-to-r from-violet-500 to-violet-700"></div>

            <div class="p-6 text-center">
                {{-- EventFlow header --}}
                <div class="flex items-center justify-center gap-2 mb-6">
                    <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-violet-500 to-violet-700 flex items-center justify-center">
                        <x-icon name="sparkles" class="w-3 h-3 text-white" />
                    </div>
                    <span class="text-xs font-semibold text-zinc-400 tracking-wider">EVENTFLOW</span>
                </div>

                {{-- Event name --}}
                <h2 class="text-lg font-semibold text-zinc-100 mb-1">{{ $ticket->event->name }}</h2>
                <p class="text-xs text-zinc-500 mb-6">{{ $ticket->ticketType->name ?? 'Ingresso' }}</p>

                {{-- QR Code --}}
                <div class="bg-white p-4 rounded-2xl mx-auto inline-block mb-6">
                    <img src="{{ $qrService->generate($ticket) }}" alt="QR Code" class="w-48 h-48">
                </div>

                {{-- Code --}}
                <div class="mb-6">
                    <div class="text-xs text-zinc-500 mb-1">Código do ingresso</div>
                    <code class="text-sm font-mono text-violet-300">{{ $ticket->code }}</code>
                </div>

                {{-- Details --}}
                <div class="text-left space-y-3 pt-6 border-t border-zinc-800">
                    <div>
                        <div class="text-xs text-zinc-500">Participante</div>
                        <div class="text-sm font-medium text-zinc-100">{{ $ticket->participant_name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500">Data</div>
                        <div class="text-sm font-medium text-zinc-100">{{ $ticket->event->starts_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-zinc-500">Local</div>
                        <div class="text-sm font-medium text-zinc-100">{{ $ticket->event->venue_name ?: $ticket->event->city }}</div>
                    </div>
                </div>

                @if($ticket->isCheckedIn())
                    <div class="mt-6 p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-lg">
                        <div class="flex items-center justify-center gap-2 text-emerald-400">
                            <x-icon name="check-circle" class="w-4 h-4" />
                            <span class="text-xs font-medium">Check-in realizado</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <p class="text-center text-xs text-zinc-500 mt-6 print:hidden">
            Apresente este QR Code na entrada do evento.
        </p>
    </main>
</x-layouts.public>