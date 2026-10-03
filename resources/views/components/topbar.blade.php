@php
    $user = auth()->user();
    $routeName = request()->route()?->getName() ?? '';
    $crumb = match (true) {
        str_contains($routeName, 'events') => 'Eventos',
        str_contains($routeName, 'orders') => 'Pedidos',
        str_contains($routeName, 'tickets') => 'Ingressos',
        str_contains($routeName, 'participants') => 'Participantes',
        str_contains($routeName, 'check-in') => 'Check-in',
        str_contains($routeName, 'waitlist') => 'Lista de Espera',
        str_contains($routeName, 'schedule') => 'Programação',
        str_contains($routeName, 'speakers') => 'Palestrantes',
        str_contains($routeName, 'tasks') => 'Tarefas',
        str_contains($routeName, 'reviews') => 'Avaliações',
        str_contains($routeName, 'team') => 'Equipe',
        str_contains($routeName, 'vendors') => 'Fornecedores',
        str_contains($routeName, 'sponsors') => 'Patrocinadores',
        str_contains($routeName, 'coupons') => 'Cupons',
        str_contains($routeName, 'expenses') => 'Despesas',
        str_contains($routeName, 'reports') => 'Relatórios',
        str_contains($routeName, 'settings') => 'Configurações',
        str_contains($routeName, 'profile') => 'Perfil',
        default => null,
    };
@endphp

<header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-zinc-800/80 bg-zinc-950/85 px-4 backdrop-blur-xl lg:px-6">
    {{-- Mobile menu --}}
    <button @click="$store.sidebar.toggleMobile()" aria-label="Abrir menu" class="icon-btn -ml-1 lg:hidden">
        <x-icon name="menu" class="h-5 w-5" />
    </button>

    {{-- Breadcrumb --}}
    @if($crumb && !str_contains($routeName, 'dashboard.index'))
        <nav class="hidden items-center gap-1.5 text-[0.8125rem] md:flex" aria-label="Navegação">
            <a href="{{ route('dashboard.index') }}" class="text-zinc-500 transition-colors hover:text-zinc-200">Dashboard</a>
            <x-icon name="chevron-right" class="h-3.5 w-3.5 text-zinc-700" />
            <span class="font-medium text-zinc-200">{{ $crumb }}</span>
        </nav>
        <div class="hidden h-5 w-px bg-zinc-800 md:block"></div>
    @endif

    {{-- Search --}}
    <button @click="$store.commandPalette.toggle()"
            class="group flex h-9.5 max-w-md flex-1 items-center gap-2.5 rounded-xl border border-zinc-800 bg-zinc-900/70 px-3 text-sm text-zinc-500 shadow-[inset_0_1px_2px_rgb(0_0_0/0.3)] transition-all hover:border-zinc-700 hover:bg-zinc-900">
        <x-icon name="search" class="h-4 w-4 shrink-0 text-zinc-600 transition-colors group-hover:text-zinc-400" />
        <span class="hidden truncate sm:inline">Buscar eventos, pedidos, pessoas…</span>
        <span class="ml-auto hidden shrink-0 items-center gap-1 lg:inline-flex">
            <kbd class="kbd">Ctrl</kbd>
            <kbd class="kbd">K</kbd>
        </span>
    </button>

    <div class="flex-1"></div>

    {{-- Public site --}}
    <a href="{{ route('public.events.index') }}" target="_blank" class="icon-btn hidden !w-auto gap-2 px-3 text-[0.8125rem] font-medium sm:inline-flex" title="Ver site público">
        <x-icon name="globe" class="h-4 w-4" />
        <span class="hidden xl:inline">Site público</span>
    </a>

    {{-- Quick actions --}}
    <a href="{{ route('dashboard.events.create') }}" class="btn btn-primary btn-sm hidden !h-9.5 !px-4 sm:inline-flex">
        <x-icon name="plus" class="h-4 w-4" />
        Novo Evento
    </a>

    {{-- Notifications --}}
    <livewire:notification-dropdown />

    {{-- User menu (mobile) --}}
    <div x-data="{ open: false }" class="relative sm:hidden">
        <button @click="open = !open" aria-label="Menu do usuário" class="flex items-center rounded-xl p-1 transition-colors hover:bg-zinc-900">
            <img src="{{ $user->avatarUrl() }}" alt="" class="avatar-ring h-8 w-8 rounded-full object-cover">
        </button>
        <div x-show="open" x-cloak x-transition.opacity.duration.150ms @click.outside="open = false"
              class="dropdown-panel absolute right-0 z-10 mt-2 w-52 shadow-2xl">
            <a href="{{ route('dashboard.profile') }}" class="dropdown-item">
                <x-icon name="user-check" class="h-4 w-4 text-zinc-500" />
                <span>Perfil</span>
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
</header>
