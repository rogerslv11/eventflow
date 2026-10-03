<div class="space-y-6" x-data="dashboardData()">
    {{-- Welcome / Hero --}}
    <div class="relative overflow-hidden card p-6 lg:p-8">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-600/10 via-transparent to-violet-900/10"></div>
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-violet-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-violet-700/10 rounded-full blur-3xl"></div>

        <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-violet-500/10 border border-violet-500/20 rounded-full text-xs font-medium text-violet-300">
                        <span class="w-1.5 h-1.5 bg-violet-400 rounded-full animate-pulse"></span>
                        {{ now()->translatedFormat('l, d \d\e F') }}
                    </span>
                </div>
                <h1 class="text-3xl font-semibold text-zinc-100">
                    Olá, {{ explode(' ', auth()->user()->name)[0] }} 👋
                </h1>
                <p class="text-sm text-zinc-400 mt-1.5">
                    Você tem <strong class="text-zinc-200">{{ $stats['active_events'] }}</strong> evento(s) ativo(s) e <strong class="text-zinc-200">{{ $stats['upcoming_events'] }}</strong> programado(s).
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.events.create') }}" class="btn btn-primary">
                    <x-icon name="plus" class="w-4 h-4" />
                    Novo evento
                </a>
                <a href="{{ route('dashboard.check-in.index') }}" class="btn btn-secondary">
                    <x-icon name="scan" class="w-4 h-4" />
                    Check-in
                </a>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Eventos --}}
        <div class="card p-5 hover:border-violet-500/30 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <x-icon name="calendar" class="w-5 h-5 text-violet-400" />
                </div>
                <a href="{{ route('dashboard.events.index') }}" class="text-xs text-zinc-500 hover:text-violet-400 transition-colors">
                    Ver →
                </a>
            </div>
            <div class="text-[1.7rem] font-semibold leading-9 tracking-tight tabular-nums text-zinc-100">{{ number_format($stats['total_events'], 0, ',', '.') }}</div>
            <div class="text-sm text-zinc-500 mt-1">Eventos no total</div>
            <div class="mt-3 pt-3 border-t border-zinc-800 flex items-center gap-3 text-xs">
                <div class="flex items-center gap-1 text-emerald-400">
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></span>
                    {{ $stats['active_events'] }} ativos
                </div>
                <div class="flex items-center gap-1 text-blue-400">
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
                    {{ $stats['upcoming_events'] }} próximos
                </div>
            </div>
        </div>

        {{-- Ingressos --}}
        <div class="card p-5 hover:border-violet-500/30 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <x-icon name="ticket" class="w-5 h-5 text-emerald-400" />
                </div>
                <span class="badge badge-success text-xs">+12.5%</span>
            </div>
            <div class="text-[1.7rem] font-semibold leading-9 tracking-tight tabular-nums text-zinc-100">{{ number_format($stats['tickets_sold'], 0, ',', '.') }}</div>
            <div class="text-sm text-zinc-500 mt-1">Ingressos vendidos</div>
            <div class="mt-3 pt-3 border-t border-zinc-800 flex items-center gap-1 text-xs text-emerald-400">
                <x-icon name="trending-up" class="w-3 h-3" />
                <span>crescimento contínuo</span>
            </div>
        </div>

        {{-- Receita --}}
        <div class="card p-5 hover:border-violet-500/30 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <x-icon name="credit-card" class="w-5 h-5 text-amber-400" />
                </div>
                <span class="badge badge-warning text-xs">+8.3%</span>
            </div>
            <div class="text-[1.7rem] font-semibold leading-9 tracking-tight tabular-nums text-zinc-100">R$ {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
            <div class="text-sm text-zinc-500 mt-1">Receita total</div>
            <div class="mt-3 pt-3 border-t border-zinc-800 flex items-center gap-1 text-xs text-amber-400">
                <x-icon name="trending-up" class="w-3 h-3" />
                <span>tendência positiva</span>
            </div>
        </div>

        {{-- Participantes --}}
        <div class="card p-5 hover:border-violet-500/30 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-pink-500/10 border border-pink-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <x-icon name="users" class="w-5 h-5 text-pink-400" />
                </div>
                <a href="{{ route('dashboard.participants.index') }}" class="text-xs text-zinc-500 hover:text-violet-400 transition-colors">
                    Ver →
                </a>
            </div>
            <div class="text-[1.7rem] font-semibold leading-9 tracking-tight tabular-nums text-zinc-100">{{ number_format($stats['participants'], 0, ',', '.') }}</div>
            <div class="text-sm text-zinc-500 mt-1">Participantes únicos</div>
            <div class="mt-3 pt-3 border-t border-zinc-800 flex items-center gap-1 text-xs text-pink-400">
                <x-icon name="user-check" class="w-3 h-3" />
                <span>{{ $stats['attendance_rate'] }}% comparecimento</span>
            </div>
        </div>
    </div>

    {{-- Secondary KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                <x-icon name="play-circle" class="w-4 h-4 text-blue-400" />
            </div>
            <div class="min-w-0">
                <div class="text-xs text-zinc-500">Em andamento</div>
                <div class="text-lg font-semibold text-zinc-100">{{ $stats['active_events'] }}</div>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-cyan-500/10 flex items-center justify-center flex-shrink-0">
                <x-icon name="clock" class="w-4 h-4 text-cyan-400" />
            </div>
            <div class="min-w-0">
                <div class="text-xs text-zinc-500">Próximos</div>
                <div class="text-lg font-semibold text-zinc-100">{{ $stats['upcoming_events'] }}</div>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                <x-icon name="check-circle" class="w-4 h-4 text-emerald-400" />
            </div>
            <div class="min-w-0">
                <div class="text-xs text-zinc-500">Check-ins</div>
                <div class="text-lg font-semibold text-zinc-100">{{ number_format($stats['check_ins']) }}</div>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-violet-500/10 flex items-center justify-center flex-shrink-0">
                <x-icon name="percent" class="w-4 h-4 text-violet-400" />
            </div>
            <div class="min-w-0">
                <div class="text-xs text-zinc-500">Taxa de presença</div>
                <div class="text-lg font-semibold gradient-text">{{ $stats['attendance_rate'] }}%</div>
            </div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Revenue Chart - bigger --}}
        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-medium text-zinc-200">Receita por período</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Últimos 30 dias · Total: <strong class="text-zinc-300">R$ {{ number_format(array_sum($revenueChart), 2, ',', '.') }}</strong></p>
                </div>
                <div class="flex items-center gap-1 p-1 bg-zinc-900 rounded-lg">
                    <button class="px-2.5 py-1 text-xs rounded bg-violet-500/10 text-violet-300">30d</button>
                    <button class="px-2.5 py-1 text-xs rounded text-zinc-400 hover:text-zinc-200">90d</button>
                    <button class="px-2.5 py-1 text-xs rounded text-zinc-400 hover:text-zinc-200">1a</button>
                </div>
            </div>
            <div x-data="chart(@json(array_values($revenueChart)), 'Receita (R$)')" class="h-72">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>

        {{-- Tickets distribution --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-medium text-zinc-200">Ingressos</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Últimos 30 dias</p>
                </div>
            </div>
            <div x-data="ticketChart(@json(array_values($ticketsChart)))" class="h-72 relative">
                <canvas x-ref="canvas"></canvas>
            </div>
            <div class="mt-4 pt-4 border-t border-zinc-800 flex items-center justify-between text-xs">
                <div>
                    <div class="text-zinc-500">Total vendido</div>
                    <div class="text-zinc-100 font-semibold mt-0.5">{{ number_format(array_sum($ticketsChart)) }}</div>
                </div>
                <div>
                    <div class="text-zinc-500">Média diária</div>
                    <div class="text-zinc-100 font-semibold mt-0.5">{{ number_format(array_sum($ticketsChart) / max(count($ticketsChart), 1), 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom row: top events + activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Top events --}}
        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-medium text-zinc-200">Top eventos</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Mais ingressos vendidos</p>
                </div>
                <a href="{{ route('dashboard.events.index') }}" class="text-xs text-violet-400 hover:text-violet-300 inline-flex items-center gap-1">
                    Ver todos
                    <x-icon name="arrow-right" class="w-3 h-3" />
                </a>
            </div>
            <div class="space-y-2">
                @forelse($topEvents as $i => $event)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-zinc-900/30 hover:bg-zinc-900/60 transition-colors group">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500/20 to-violet-700/20 border border-violet-500/20 flex items-center justify-center flex-shrink-0 text-violet-300 text-xs font-semibold">
                            #{{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-zinc-100 truncate">{{ $event['name'] }}</div>
                            <div class="text-xs text-zinc-500">{{ number_format($event['count']) }} ingressos</div>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <div class="w-32 h-1.5 bg-zinc-800 rounded-full overflow-hidden hidden sm:block">
                                @php
                                    $maxCount = max(array_column($topEvents, 'count'));
                                    $percent = $maxCount > 0 ? ($event['count'] / $maxCount) * 100 : 0;
                                @endphp
                                <div class="h-full bg-gradient-to-r from-violet-500 to-violet-700 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-zinc-100 w-14 text-right">{{ number_format($event['count']) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <x-icon name="calendar" class="w-10 h-10 text-zinc-700 mx-auto mb-3" />
                        <p class="text-sm text-zinc-500">Nenhum evento com ingressos vendidos ainda.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Activity --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-medium text-zinc-200">Atividade recente</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Últimas ações</p>
                </div>
                <a href="{{ route('dashboard.activity.index') }}" class="text-xs text-violet-400 hover:text-violet-300">
                    Ver tudo
                </a>
            </div>
            <div class="space-y-2 max-h-96 overflow-y-auto pr-1">
                @forelse($recentActivity as $log)
                    <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-zinc-900/40 transition-colors">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                            @switch($log['action'])
                                @case('created') bg-emerald-500/10 text-emerald-400 @break
                                @case('login') bg-blue-500/10 text-blue-400 @break
                                @case('check_in') bg-violet-500/10 text-violet-400 @break
                                @case('purchase') bg-amber-500/10 text-amber-400 @break
                                @case('logout') bg-zinc-700/30 text-zinc-400 @break
                                @case('deleted') bg-red-500/10 text-red-400 @break
                                @default bg-zinc-800 text-zinc-400
                            @endswitch">
                            <x-icon :name="match($log['action']) {
                                'created' => 'plus',
                                'login' => 'log-in',
                                'check_in' => 'check-circle',
                                'purchase' => 'shopping-cart',
                                'logout' => 'log-out',
                                'deleted' => 'trash',
                                'updated' => 'edit',
                                default => 'bell',
                            }" class="w-3.5 h-3.5" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-zinc-300 leading-snug">
                                <span class="font-medium">{{ $log['user']['name'] ?? 'Sistema' }}</span>
                                <span class="text-zinc-500">·</span>
                                {{ ucfirst($log['action']) }}
                            </p>
                            <p class="text-xs text-zinc-500 mt-0.5">{{ \Carbon\Carbon::parse($log['performed_at'])->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <x-icon name="bell" class="w-8 h-8 text-zinc-700 mx-auto mb-2" />
                        <p class="text-sm text-zinc-500">Nenhuma atividade.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function dashboardData() {
        return {};
    }

    function chart(data, label) {
        return {
            init() { this.render(); },
            render() {
                const ctx = this.$refs.canvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(124, 58, 237, 0.5)');
                gradient.addColorStop(1, 'rgba(124, 58, 237, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map((_, i) => {
                            const d = new Date();
                            d.setDate(d.getDate() - (data.length - 1 - i));
                            return d.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' });
                        }),
                        datasets: [{
                            label: label,
                            data: data,
                            borderColor: '#7C3AED',
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2.5,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#7C3AED',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#18181b',
                                titleColor: '#fff',
                                bodyColor: '#a1a1aa',
                                borderColor: '#27272a',
                                borderWidth: 1,
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    label: (ctx) => 'R$ ' + ctx.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2 })
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#71717a', font: { size: 10 }, maxTicksLimit: 8 },
                                border: { display: false },
                            },
                            y: {
                                grid: { color: 'rgba(63, 63, 70, 0.2)', drawBorder: false },
                                ticks: {
                                    color: '#71717a', font: { size: 10 },
                                    callback: (v) => 'R$ ' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v)
                                },
                                beginAtZero: true,
                            }
                        }
                    }
                });
            }
        }
    }

    function ticketChart(data) {
        return {
            init() { this.render(); },
            render() {
                const ctx = this.$refs.canvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(16, 185, 129, 0.5)');
                gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map((_, i) => {
                            const d = new Date();
                            d.setDate(d.getDate() - (data.length - 1 - i));
                            return d.toLocaleDateString('pt-BR', { day: '2-digit' });
                        }),
                        datasets: [{
                            data: data,
                            backgroundColor: gradient,
                            borderColor: '#10b981',
                            borderWidth: 1,
                            borderRadius: 4,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#18181b',
                                titleColor: '#fff',
                                bodyColor: '#a1a1aa',
                                borderColor: '#27272a',
                                borderWidth: 1,
                                padding: 12,
                                displayColors: false,
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: '#71717a', font: { size: 10 } }, border: { display: false } },
                            y: { grid: { color: 'rgba(63, 63, 70, 0.2)', drawBorder: false }, ticks: { color: '#71717a', font: { size: 10 } }, beginAtZero: true }
                        }
                    }
                });
            }
        }
    }
</script>
@endpush