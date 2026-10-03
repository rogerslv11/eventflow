@php
    $user = auth()->user();
    $organization = $user?->organizations()->first();

    $sections = [
        [
            'label' => 'Visão geral',
            'items' => [
                ['name' => 'Dashboard', 'route' => 'dashboard.index', 'icon' => 'home', 'permission' => null],
            ],
        ],
        [
            'label' => 'Operação',
            'items' => [
                ['name' => 'Eventos', 'route' => 'dashboard.events.index', 'icon' => 'calendar', 'permission' => 'events.view'],
                ['name' => 'Pedidos', 'route' => 'dashboard.orders.index', 'icon' => 'shopping-cart', 'permission' => null],
                ['name' => 'Ingressos', 'route' => 'dashboard.tickets.index', 'icon' => 'ticket', 'permission' => 'tickets.view'],
                ['name' => 'Participantes', 'route' => 'dashboard.participants.index', 'icon' => 'users', 'permission' => 'participants.view'],
                ['name' => 'Check-in', 'route' => 'dashboard.check-in.index', 'icon' => 'scan', 'permission' => 'check_in.view'],
                ['name' => 'Lista de Espera', 'route' => 'dashboard.waitlist.index', 'icon' => 'clock', 'permission' => null],
            ],
        ],
        [
            'label' => 'Conteúdo',
            'items' => [
                ['name' => 'Programação', 'route' => 'dashboard.schedule.index', 'icon' => 'clock', 'permission' => null],
                ['name' => 'Palestrantes', 'route' => 'dashboard.speakers.index', 'icon' => 'mic', 'permission' => null],
                ['name' => 'Tarefas', 'route' => 'dashboard.tasks.index', 'icon' => 'check', 'permission' => null],
                ['name' => 'Avaliações', 'route' => 'dashboard.reviews.index', 'icon' => 'star', 'permission' => null],
            ],
        ],
        [
            'label' => 'Negócios',
            'items' => [
                ['name' => 'Equipe', 'route' => 'dashboard.team.index', 'icon' => 'user-check', 'permission' => null],
                ['name' => 'Fornecedores', 'route' => 'dashboard.vendors.index', 'icon' => 'truck', 'permission' => null],
                ['name' => 'Patrocinadores', 'route' => 'dashboard.sponsors.index', 'icon' => 'star', 'permission' => null],
                ['name' => 'Cupons', 'route' => 'dashboard.coupons.index', 'icon' => 'tag', 'permission' => null],
                ['name' => 'Despesas', 'route' => 'dashboard.expenses.index', 'icon' => 'file-text', 'permission' => null],
                ['name' => 'Relatórios', 'route' => 'dashboard.reports.index', 'icon' => 'file-text', 'permission' => 'reports.view'],
            ],
        ],
    ];

    $isActive = fn (string $route) => request()->routeIs($route) || request()->routeIs($route . '.*');
@endphp

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-zinc-800/80 bg-[#0c0c0f]/95 backdrop-blur transition-all duration-300 ease-in-out"
    :class="[
        $store.sidebar.mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        $store.sidebar.collapsed ? 'lg:w-[4.75rem]' : 'lg:w-64',
    ]">

    {{-- Brand --}}
    <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-zinc-800/80 px-4">
        <a href="{{ route('dashboard.index') }}" class="flex min-w-0 items-center gap-2.5">
            <div class="relative flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-violet-400 via-violet-600 to-violet-800 shadow-[0_4px_16px_-4px_rgb(124_58_237/0.7)]">
                <x-icon name="sparkles" class="h-4.5 w-4.5 text-white" />
                <div class="absolute inset-0 rounded-xl ring-1 ring-inset ring-white/20"></div>
            </div>
            <div x-show="!$store.sidebar.collapsed" x-cloak class="min-w-0 leading-tight">
                <div class="truncate text-[0.9375rem] font-semibold tracking-tight text-zinc-50">EventFlow</div>
                <div class="truncate text-[0.6875rem] font-medium text-zinc-500">{{ $organization?->name ?? 'Workspace' }}</div>
            </div>
        </a>
    </div>

    {{-- Collapse toggle (desktop) --}}
    <button @click="$store.sidebar.toggle()" aria-label="Recolher menu"
        class="absolute -right-3 top-[4.5rem] z-10 hidden h-6 w-6 items-center justify-center rounded-full border border-zinc-700/80 bg-zinc-900 text-zinc-400 shadow-lg transition-colors hover:bg-zinc-800 hover:text-zinc-100 lg:flex">
        <x-icon name="chevron-left" x-show="!$store.sidebar.collapsed" class="h-3.5 w-3.5" />
        <x-icon name="chevron-right" x-show="$store.sidebar.collapsed" x-cloak class="h-3.5 w-3.5" />
    </button>

    {{-- Nav --}}
    <nav class="flex-1 space-y-4 overflow-y-auto px-3 py-4">
        @foreach($sections as $section)
            <div>
                <div x-show="!$store.sidebar.collapsed" x-cloak class="sidebar-section !pt-0">{{ $section['label'] }}</div>
                <div x-show="$store.sidebar.collapsed" x-cloak class="mx-2 mb-1 border-t border-zinc-800/80"></div>
                <div class="space-y-0.5">
                    @foreach($section['items'] as $item)
                        @php $active = $isActive($item['route']); @endphp
                        <a href="{{ route_exists($item['route']) ? route($item['route']) : '#' }}"
                           title="{{ $item['name'] }}"
                           class="sidebar-link {{ $active ? 'active' : '' }}">
                            <x-icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
                            <span x-show="!$store.sidebar.collapsed" x-cloak class="truncate">{{ $item['name'] }}</span>
                            @if($active)
                                <span x-show="!$store.sidebar.collapsed" x-cloak class="ml-auto h-1.5 w-1.5 shrink-0 rounded-full bg-violet-400"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    {{-- Bottom --}}
    <div class="shrink-0 space-y-0.5 border-t border-zinc-800/80 px-3 py-3">
        <a href="{{ route_exists('dashboard.settings.index') ? route('dashboard.settings.index') : '#' }}"
           class="sidebar-link {{ request()->routeIs('dashboard.settings.*') ? 'active' : '' }}">
            <x-icon name="settings" class="h-4 w-4 shrink-0" />
            <span x-show="!$store.sidebar.collapsed" x-cloak>Configurações</span>
        </a>

        {{-- User --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="sidebar-link w-full !gap-2.5">
                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="avatar-ring h-7 w-7 shrink-0 rounded-full object-cover">
                <div x-show="!$store.sidebar.collapsed" x-cloak class="min-w-0 flex-1 text-left leading-tight">
                    <div class="truncate text-[0.8125rem] font-semibold text-zinc-100">{{ $user->name }}</div>
                    <div class="truncate text-[0.6875rem] text-zinc-500">{{ $organization?->name ?? 'Sem organização' }}</div>
                </div>
                <x-icon name="chevron-up" x-show="!$store.sidebar.collapsed" class="h-4 w-4 shrink-0 text-zinc-500" />
            </button>

            <div x-show="open"
                 x-cloak
                 @click.outside="open = false"
                 x-transition.opacity.duration.150ms
                 class="dropdown-panel absolute inset-x-0 bottom-full z-10 mb-2 shadow-2xl">
                <a href="{{ route('dashboard.profile') }}" class="dropdown-item">
                    <x-icon name="user-check" class="h-4 w-4 text-zinc-500" />
                    <span>Meu Perfil</span>
                </a>
                <a href="{{ route('dashboard.settings.index') }}" class="dropdown-item">
                    <x-icon name="settings" class="h-4 w-4 text-zinc-500" />
                    <span>Configurações</span>
                </a>
                <div class="my-1 border-t border-zinc-800"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item !text-red-400 hover:!bg-red-500/10">
                        <x-icon name="log-out" class="h-4 w-4" />
                        <span>Sair</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
