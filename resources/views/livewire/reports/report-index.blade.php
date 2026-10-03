<div class="space-y-6">
    <x-page-header title="Relatórios" subtitle="Análises detalhadas dos seus eventos e vendas." eyebrow="Inteligência">
        <x-slot:actions>
            <button wire:click="exportCsv" class="btn btn-secondary">
                <x-icon name="download" class="w-4 h-4" />
                Exportar CSV
            </button>
            <button class="btn btn-primary">
                <x-icon name="file-text" class="w-4 h-4" />
                Gerar PDF
            </button>
        </x-slot:actions>
    </x-page-header>

    {{-- Filters --}}
    <div class="toolbar">
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <div class="flex-1">
                <label class="text-xs text-zinc-500 mb-1.5 block">Evento</label>
                <select wire:model.live="filterEvent" class="input">
                    <option value="">Todos os eventos</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="text-xs text-zinc-500 mb-1.5 block">Período</label>
                <div class="flex items-center gap-1 p-1 bg-zinc-900 rounded-lg">
                    @foreach(['7' => '7 dias', '30' => '30 dias', '90' => '90 dias', 'all' => 'Tudo'] as $value => $label)
                        <button wire:click="$set('period', '{{ $value }}')"
                                class="flex-1 px-3 py-1.5 text-xs rounded transition-all {{ $period === $value ? 'bg-violet-500/10 text-violet-300' : 'text-zinc-400 hover:text-zinc-200' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Hero --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="card p-5 hover:border-violet-500/30 transition-all">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-lg bg-blue-500/10 flex items-center justify-center">
                    <x-icon name="shopping-cart" class="w-4 h-4 text-blue-400" />
                </div>
            </div>
            <div class="text-2xl font-semibold text-zinc-100">{{ number_format($stats['orders_total']) }}</div>
            <div class="text-xs text-zinc-500 mt-0.5">Total de pedidos</div>
            <div class="mt-2 text-xs text-emerald-400">
                {{ $stats['orders_paid'] }} pagos
            </div>
        </div>

        <div class="card p-5 hover:border-violet-500/30 transition-all">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                    <x-icon name="credit-card" class="w-4 h-4 text-emerald-400" />
                </div>
            </div>
            <div class="text-2xl font-semibold text-zinc-100">R$ {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
            <div class="text-xs text-zinc-500 mt-0.5">Receita total</div>
            @if($stats['revenue'] > 0 && $stats['orders_paid'] > 0)
                <div class="mt-2 text-xs text-emerald-400">
                    Ticket médio: R$ {{ number_format($stats['revenue'] / $stats['orders_paid'], 2, ',', '.') }}
                </div>
            @endif
        </div>

        <div class="card p-5 hover:border-violet-500/30 transition-all">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-lg bg-violet-500/10 flex items-center justify-center">
                    <x-icon name="ticket" class="w-4 h-4 text-violet-400" />
                </div>
            </div>
            <div class="text-2xl font-semibold text-zinc-100">{{ number_format($stats['tickets_sold']) }}</div>
            <div class="text-xs text-zinc-500 mt-0.5">Ingressos vendidos</div>
        </div>

        <div class="card p-5 hover:border-violet-500/30 transition-all">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-lg bg-pink-500/10 flex items-center justify-center">
                    <x-icon name="users" class="w-4 h-4 text-pink-400" />
                </div>
            </div>
            <div class="text-2xl font-semibold text-zinc-100">{{ number_format($stats['participants']) }}</div>
            <div class="text-xs text-zinc-500 mt-0.5">Participantes</div>
        </div>

        <div class="card p-5 hover:border-violet-500/30 transition-all">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-lg bg-amber-500/10 flex items-center justify-center">
                    <x-icon name="check-circle" class="w-4 h-4 text-amber-400" />
                </div>
            </div>
            <div class="text-2xl font-semibold text-zinc-100">{{ number_format($stats['check_ins']) }}</div>
            <div class="text-xs text-zinc-500 mt-0.5">Check-ins</div>
        </div>

        <div class="card p-5 hover:border-violet-500/30 transition-all bg-gradient-to-br from-violet-500/5 to-transparent">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-lg bg-violet-500/10 flex items-center justify-center">
                    <x-icon name="trending-up" class="w-4 h-4 text-violet-400" />
                </div>
            </div>
            <div class="text-2xl font-semibold gradient-text">{{ $stats['attendance_rate'] }}%</div>
            <div class="text-xs text-zinc-500 mt-0.5">Taxa de comparecimento</div>
            @if($stats['attendance_rate'] >= 70)
                <div class="mt-2 text-xs text-emerald-400">Excelente!</div>
            @elseif($stats['attendance_rate'] >= 50)
                <div class="mt-2 text-xs text-amber-400">Bom desempenho</div>
            @else
                <div class="mt-2 text-xs text-red-400">Atenção necessária</div>
            @endif
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Revenue Trend --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-medium text-zinc-100">Evolução de receita</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Últimos {{ $period === 'all' ? '90' : $period }} dias</p>
                </div>
            </div>
            @if(array_sum($revenueChart) > 0)
                <div x-data="lineChart(@json(array_values($revenueChart)), 'Receita')" class="h-72">
                    <canvas x-ref="canvas"></canvas>
                </div>
            @else
                <div class="h-72 flex items-center justify-center text-sm text-zinc-500">
                    Sem dados no período selecionado.
                </div>
            @endif
        </div>

        {{-- Tickets Status --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-medium text-zinc-100">Status dos pedidos</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Distribuição</p>
                </div>
            </div>
            <div x-data="doughnutChart(@json($ordersChart))" class="h-72 relative">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>
    </div>

    {{-- Top events --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-medium text-zinc-100">Ranking de eventos</h3>
                <p class="text-xs text-zinc-500 mt-0.5">Top eventos por receita</p>
            </div>
            <span class="badge badge-neutral">{{ count($salesByEvent) }} eventos</span>
        </div>
        @if($salesByEvent->count() > 0)
            <div class="table-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-left py-3 px-2 font-medium">#</th>
                            <th class="text-left py-3 px-2 font-medium">Evento</th>
                            <th class="text-right py-3 px-2 font-medium">Ingressos</th>
                            <th class="text-right py-3 px-2 font-medium">Receita</th>
                            <th class="text-left py-3 px-2 font-medium">Desempenho</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByEvent as $i => $event)
                            @php
                                $maxRevenue = max($salesByEvent->pluck('orders_sum_total')->toArray() ?: [0]);
                                $percent = $maxRevenue > 0 ? (($event->orders_sum_total ?? 0) / $maxRevenue) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-zinc-900/30 transition-colors">
                                <td class="py-3 px-2">
                                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-violet-500/20 to-violet-700/20 border border-violet-500/20 flex items-center justify-center text-xs font-semibold text-violet-300">
                                        {{ $i + 1 }}
                                    </div>
                                </td>
                                <td class="py-3 px-2">
                                    <div class="font-medium text-zinc-100">{{ $event->name }}</div>
                                </td>
                                <td class="py-3 px-2 text-right text-zinc-300">{{ number_format($event->tickets_count) }}</td>
                                <td class="py-3 px-2 text-right">
                                    <div class="font-semibold text-zinc-100">R$ {{ number_format($event->orders_sum_total ?? 0, 2, ',', '.') }}</div>
                                </td>
                                <td class="py-3 px-2">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 bg-zinc-800 rounded-full overflow-hidden max-w-xs">
                                            <div class="h-full bg-gradient-to-r from-violet-500 to-violet-700 rounded-full" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <span class="text-xs text-zinc-500 w-10 text-right">{{ round($percent) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-12 text-center">
                <x-icon name="calendar" class="w-10 h-10 text-zinc-700 mx-auto mb-3" />
                <p class="text-sm text-zinc-500">Nenhuma venda registrada no período.</p>
            </div>
        @endif
    </div>

    {{-- Coupons and Check-ins row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Coupons --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-medium text-zinc-100">Top cupons</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Mais utilizados</p>
                </div>
                <x-icon name="tag" class="w-4 h-4 text-zinc-500" />
            </div>
            <div class="space-y-2">
                @forelse($topCoupons as $coupon)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-zinc-900/30 hover:bg-zinc-900/60 transition-colors">
                        <code class="px-2.5 py-1 bg-violet-500/10 text-violet-300 rounded text-sm font-mono font-semibold flex-shrink-0">
                            {{ $coupon->code }}
                        </code>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs text-zinc-500">
                                @if($coupon->type === \App\Enums\CouponType::Percentage)
                                    {{ $coupon->value }}% de desconto
                                @else
                                    R$ {{ number_format($coupon->value, 2, ',', '.') }} off
                                @endif
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-sm font-semibold text-zinc-100">{{ $coupon->usages_count }}</div>
                            <div class="text-xs text-zinc-500">usos</div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <x-icon name="tag" class="w-10 h-10 text-zinc-700 mx-auto mb-3" />
                        <p class="text-sm text-zinc-500">Nenhum cupom utilizado no período.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Check-ins by day --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-medium text-zinc-100">Check-ins por dia</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Volume diário</p>
                </div>
                <x-icon name="check-circle" class="w-4 h-4 text-zinc-500" />
            </div>
            @if($checkInsByDay->count() > 0)
                <div class="table-scroll">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-left py-2 px-2 font-medium">Data</th>
                                <th class="text-right py-2 px-2 font-medium">Check-ins</th>
                                <th class="text-left py-2 px-2 font-medium">Volume</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $maxCI = max($checkInsByDay->pluck('count')->toArray() ?: [1]);
                            @endphp
                            @foreach($checkInsByDay as $row)
                                <tr>
                                    <td class="py-2.5 px-2 text-zinc-300">{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                                    <td class="py-2.5 px-2 text-right font-semibold text-zinc-100">{{ $row->count }}</td>
                                    <td class="py-2.5 px-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-24 h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-400 rounded-full" style="width: {{ ($row->count / $maxCI) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-8 text-center">
                    <x-icon name="check-circle" class="w-10 h-10 text-zinc-700 mx-auto mb-3" />
                    <p class="text-sm text-zinc-500">Sem check-ins no período.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function lineChart(data, label) {
        return {
            init() { this.render(); },
            render() {
                const ctx = this.$refs.canvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(124, 58, 237, 0.4)');
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
                            x: { grid: { display: false }, ticks: { color: '#71717a', font: { size: 10 }, maxTicksLimit: 8 }, border: { display: false } },
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

    function doughnutChart(data) {
        return {
            init() { this.render(); },
            render() {
                const ctx = this.$refs.canvas.getContext('2d');
                const total = data.reduce((a, b) => a + b, 0);
                const labels = ['Pagos', 'Pendentes'];

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: ['#10b981', '#3f3f46'],
                            borderColor: '#18181b',
                            borderWidth: 3,
                            hoverOffset: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#a1a1aa', font: { size: 11 }, padding: 12, usePointStyle: true, pointStyle: 'circle' }
                            },
                            tooltip: {
                                backgroundColor: '#18181b',
                                titleColor: '#fff',
                                bodyColor: '#a1a1aa',
                                borderColor: '#27272a',
                                borderWidth: 1,
                                padding: 12,
                                callbacks: {
                                    label: (ctx) => {
                                        const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                        return ` ${ctx.label}: ${ctx.parsed} (${pct}%)`;
                                    }
                                }
                            }
                        }
                    },
                    plugins: [{
                        id: 'centerText',
                        beforeDraw(chart) {
                            const { width, height, ctx } = chart;
                            ctx.restore();
                            const fontSize = (height / 8).toFixed(2);
                            ctx.font = `bold ${fontSize}px sans-serif`;
                            ctx.textBaseline = 'middle';
                            ctx.textAlign = 'center';
                            ctx.fillStyle = '#e4e4e7';
                            const text = total.toString();
                            ctx.fillText(text, width / 2, height / 2 - 10);
                            ctx.font = `11px sans-serif`;
                            ctx.fillStyle = '#71717a';
                            ctx.fillText('total', width / 2, height / 2 + 12);
                            ctx.save();
                        }
                    }]
                });
            }
        }
    }
</script>
@endpush