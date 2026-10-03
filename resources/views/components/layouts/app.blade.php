<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'EventFlow' }} — EventFlow</title>
    <meta name="description" content="EventFlow - Plataforma completa para gestão de eventos, ingressos, participantes, equipes e check-in.">
    <meta name="theme-color" content="#09090b">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="app-bg min-h-screen bg-zinc-950 text-zinc-100 antialiased" x-data>
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <x-sidebar />

        {{-- Main --}}
        <div class="flex min-w-0 flex-1 flex-col transition-[margin] duration-300"
             :style="`margin-left: ${window.innerWidth < 1024 ? '0' : ($store.sidebar.collapsed ? '4.75rem' : '16rem')}`">

            {{-- Top bar --}}
            <x-topbar />

            {{-- Page content --}}
            <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 lg:px-8 lg:py-8">
                <div class="animate-fade-in">
                    {{ $slot }}
                </div>
            </main>

            {{-- Footer --}}
            <footer class="border-t border-zinc-800/80 px-6 py-4 text-xs text-zinc-600">
                <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4">
                    <span class="flex items-center gap-2">
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        © {{ date('Y') }} EventFlow · Todos os sistemas operacionais
                    </span>
                    <span class="tabular-nums">v1.1.0</span>
                </div>
            </footer>
        </div>
    </div>

    {{-- Mobile sidebar overlay --}}
    <div x-show="$store.sidebar.mobileOpen"
         x-cloak
         x-transition.opacity
         class="fixed inset-0 z-40 bg-black/70 backdrop-blur-sm lg:hidden"
         @click="$store.sidebar.closeMobile()"></div>

    {{-- Command Palette --}}
    <x-command-palette />

    {{-- Toasts --}}
    <x-toast-stack />

    @livewireScripts
    @stack('scripts')
</body>
</html>
