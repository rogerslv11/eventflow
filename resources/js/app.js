// Register Alpine stores IMMEDIATELY when the script loads
document.addEventListener('alpine:init', () => {
    registerAlpineStores(window.Alpine);
});

// Fallback - if Alpine is already loaded
if (window.Alpine) {
    try {
        registerAlpineStores(window.Alpine);
    } catch (e) {
        // Ignore - might be already registered
    }
}

// Chart.js global
import {
    Chart,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    LineController,
    BarController,
    DoughnutController,
    Title,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';

Chart.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    ArcElement,
    LineController,
    BarController,
    DoughnutController,
    Title,
    Tooltip,
    Legend,
    Filler,
);
window.Chart = Chart;

function registerAlpineStores(Alpine) {
    if (!Alpine || typeof Alpine.store !== 'function') return;

    try {
        if (Alpine.store('toasts')) return;
    } catch (e) {
        // Store doesn't exist yet, proceed
    }

    Alpine.store('toasts', {
        items: [],
        add(toast) {
            const id = Math.random().toString(36).slice(2);
            this.items.push({ id, ...toast });
            setTimeout(() => this.dismiss(id), toast.duration || 4000);
        },
        success(message, title = 'Sucesso') { this.add({ type: 'success', title, message }); },
        error(message, title = 'Erro') { this.add({ type: 'error', title, message }); },
        warning(message, title = 'Atenção') { this.add({ type: 'warning', title, message }); },
        info(message, title = 'Info') { this.add({ type: 'info', title, message }); },
        dismiss(id) { this.items = this.items.filter(t => t.id !== id); },
    });

    Alpine.store('commandPalette', {
        open: false, query: '',
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.query = '';
                setTimeout(() => document.getElementById('command-palette-input')?.focus(), 50);
            }
        },
        close() { this.open = false; },
    });

    Alpine.store('sidebar', {
        collapsed: false,
        mobileOpen: false,
        toggle() { this.collapsed = !this.collapsed; },
        toggleMobile() { this.mobileOpen = !this.mobileOpen; },
        closeMobile() { this.mobileOpen = false; },
    });
}

// Make Alpine magic helpers available globally so Livewire components can use them
window.AlpineHelpers = {
    chart(data, label) {
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
        };
    },

    ticketChart(data) {
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
        };
    },

    lineChart(data, label) {
        return window.AlpineHelpers.chart(data, label);
    },

    doughnutChart(data) {
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
        };
    },

    dashboardData() {
        return {};
    }
};

// Make helpers available globally so Alpine x-data can find them
window.chart = window.AlpineHelpers.chart;
window.ticketChart = window.AlpineHelpers.ticketChart;
window.lineChart = window.AlpineHelpers.lineChart;
window.doughnutChart = window.AlpineHelpers.doughnutChart;
window.dashboardData = window.AlpineHelpers.dashboardData;

// Listen for Livewire events
document.addEventListener('livewire:init', () => {
    Livewire.on('toast', (data) => {
        const toastData = Array.isArray(data) ? data[0] : data;
        if (window.Alpine && window.Alpine.store('toasts')) {
            window.Alpine.store('toasts').add(toastData);
        }
    });

    Livewire.on('toast-success', (message) => {
        if (window.Alpine && window.Alpine.store('toasts')) {
            window.Alpine.store('toasts').success(message);
        }
    });

    Livewire.on('toast-error', (message) => {
        if (window.Alpine && window.Alpine.store('toasts')) {
            window.Alpine.store('toasts').error(message);
        }
    });
});

// Global keyboard shortcuts
document.addEventListener('keydown', (e) => {
    if (!window.Alpine) return;

    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        try {
            window.Alpine.store('commandPalette').toggle();
        } catch (e) {}
    }

    if (e.key === 'Escape') {
        try {
            window.Alpine.store('commandPalette').close();
            window.Alpine.store('sidebar').closeMobile();
        } catch (e) {}
    }
});

// Helper functions
window.formatCurrency = function (value) {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};
window.formatDate = function (date, format = 'd/m/Y') {
    if (!date) return '';
    return new Date(date).toLocaleDateString('pt-BR');
};
window.formatDateTime = function (date) {
    if (!date) return '';
    return new Date(date).toLocaleString('pt-BR');
};