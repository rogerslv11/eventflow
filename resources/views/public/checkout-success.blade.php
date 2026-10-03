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
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 lg:px-6 py-12">
        {{-- Success message --}}
        <div class="text-center mb-8">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-emerald-500/10 flex items-center justify-center animate-pulse-glow">
                <x-icon name="check-circle" class="w-12 h-12 text-emerald-400" />
            </div>
            <h1 class="text-3xl font-semibold text-zinc-100 mb-2">Compra confirmada!</h1>
            <p class="text-zinc-400">Seu pedido foi processado com sucesso.</p>
        </div>

        {{-- Order details --}}
        <div class="card p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="text-xs text-zinc-500">Código do pedido</div>
                    <code class="text-violet-300 font-mono">{{ $order->code }}</code>
                </div>
                <div class="text-right">
                    <div class="text-xs text-zinc-500">Total</div>
                    <div class="text-2xl font-semibold gradient-text">R$ {{ number_format($order->total, 2, ',', '.') }}</div>
                </div>
            </div>

            <div class="pt-4 border-t border-zinc-800">
                <h3 class="text-sm font-medium text-zinc-200 mb-3">{{ $order->event->name }}</h3>
                <div class="flex items-center gap-4 text-xs text-zinc-500">
                    <div class="flex items-center gap-1">
                        <x-icon name="calendar" class="w-3.5 h-3.5" />
                        {{ $order->event->starts_at->format('d M Y, H:i') }}
                    </div>
                    <div class="flex items-center gap-1">
                        <x-icon name="map-pin" class="w-3.5 h-3.5" />
                        {{ $order->event->venue_name ?: $order->event->city }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Tickets --}}
        <div class="space-y-3">
            <h3 class="text-sm font-medium text-zinc-200">Seus ingressos</h3>
            @foreach($order->tickets as $ticket)
                <a href="{{ route('public.ticket', $ticket->code) }}" class="card p-5 flex items-center gap-4 hover:border-violet-500/30 transition-colors block">
                    <div class="w-12 h-12 rounded-lg bg-violet-500/10 flex items-center justify-center flex-shrink-0">
                        <x-icon name="qr-code" class="w-6 h-6 text-violet-400" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-zinc-100">{{ $ticket->participant_name }}</div>
                        <div class="text-xs text-zinc-500">{{ $ticket->ticketType->name ?? 'Ingresso' }} • {{ $ticket->code }}</div>
                    </div>
                    <x-icon name="arrow-right" class="w-4 h-4 text-zinc-500" />
                </a>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <p class="text-sm text-zinc-500 mb-4">
                Um e-mail de confirmação foi enviado para <strong class="text-zinc-300">{{ $order->customer_email }}</strong>.
            </p>
            <a href="{{ route('public.events.index') }}" class="btn btn-secondary">
                Ver mais eventos
            </a>
        </div>
    </main>
</x-layouts.public>