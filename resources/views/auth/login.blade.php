<x-layouts.auth>
    <div class="min-h-screen flex">
        {{-- Left side: Form --}}
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">
                {{-- Logo --}}
                <div class="mb-10">
                    <a href="/" class="inline-flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-violet-700 flex items-center justify-center">
                            <x-icon name="sparkles" class="w-5 h-5 text-white" />
                        </div>
                        <span class="text-xl font-semibold text-zinc-100">EventFlow</span>
                    </a>
                </div>

                {{-- Header --}}
                <div class="mb-8">
                    <h1 class="text-2xl font-semibold text-zinc-100 mb-2">Bem-vindo de volta</h1>
                    <p class="text-sm text-zinc-400">Entre com sua conta para acessar a plataforma.</p>
                </div>

                {{-- Errors --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                        <div class="flex items-start gap-3">
                            <x-icon name="alert-triangle" class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" />
                            <div class="flex-1 text-sm text-red-300">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="label">E-mail</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="input" placeholder="seu@email.com">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="label mb-0">Senha</label>
                            <a href="{{ route('password.request') }}" class="text-xs text-violet-400 hover:text-violet-300">
                                Esqueceu a senha?
                            </a>
                        </div>
                        <input type="password" name="password" required
                               class="input" placeholder="••••••••">
                    </div>

                    <label class="flex items-center gap-2 text-sm text-zinc-300 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-zinc-700 bg-zinc-900 text-violet-600 focus:ring-violet-500 focus:ring-offset-zinc-950">
                        Lembrar-me
                    </label>

                    <button type="submit" class="btn btn-primary w-full">
                        <x-icon name="log-in" class="w-4 h-4" />
                        Entrar
                    </button>
                </form>

                {{-- Demo creds --}}
                <div class="mt-6 p-4 bg-zinc-900 border border-zinc-800 rounded-xl">
                    <div class="text-xs font-medium text-zinc-400 uppercase tracking-wider mb-2">Credenciais demo</div>
                    <div class="space-y-1 text-xs">
                        <div class="flex items-center justify-between text-zinc-300">
                            <span>E-mail:</span>
                            <code class="text-violet-300">demo@eventflow.app</code>
                        </div>
                        <div class="flex items-center justify-between text-zinc-300">
                            <span>Senha:</span>
                            <code class="text-violet-300">password</code>
                        </div>
                    </div>
                </div>

                {{-- Register link --}}
                <p class="mt-8 text-center text-sm text-zinc-400">
                    Não tem uma conta?
                    <a href="{{ route('register') }}" class="text-violet-400 hover:text-violet-300 font-medium">
                        Criar conta
                    </a>
                </p>
            </div>
        </div>

        {{-- Right side: Visual --}}
        <div class="hidden lg:flex flex-1 relative overflow-hidden bg-gradient-to-br from-violet-900/30 via-zinc-900 to-zinc-950 items-center justify-center p-12">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 30% 30%, #7C3AED 0%, transparent 40%), radial-gradient(circle at 70% 70%, #4C1D95 0%, transparent 40%);"></div>

            <div class="relative max-w-lg">
                <div class="mb-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-violet-500/10 border border-violet-500/20 rounded-full text-xs font-medium text-violet-300 mb-6">
                        <span class="w-1.5 h-1.5 bg-violet-400 rounded-full animate-pulse"></span>
                        Plataforma completa de eventos
                    </div>
                    <h2 class="text-4xl font-semibold text-zinc-100 mb-4 leading-tight">
                        Gerencie seus eventos
                        <span class="gradient-text"> de ponta a ponta</span>
                    </h2>
                    <p class="text-zinc-400 leading-relaxed">
                        Ingressos, participantes, check-in, equipes, fornecedores e relatórios — tudo em um só lugar.
                    </p>
                </div>

                {{-- Feature cards --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="card p-4">
                        <div class="w-9 h-9 rounded-lg bg-violet-500/10 flex items-center justify-center mb-3">
                            <x-icon name="ticket" class="w-4.5 h-4.5 text-violet-400" />
                        </div>
                        <div class="text-sm font-medium text-zinc-200">Ingressos</div>
                        <div class="text-xs text-zinc-500 mt-0.5">Múltiplos lotes</div>
                    </div>
                    <div class="card p-4">
                        <div class="w-9 h-9 rounded-lg bg-emerald-500/10 flex items-center justify-center mb-3">
                            <x-icon name="scan" class="w-4.5 h-4.5 text-emerald-400" />
                        </div>
                        <div class="text-sm font-medium text-zinc-200">Check-in</div>
                        <div class="text-xs text-zinc-500 mt-0.5">QR Code em tempo real</div>
                    </div>
                    <div class="card p-4">
                        <div class="w-9 h-9 rounded-lg bg-blue-500/10 flex items-center justify-center mb-3">
                            <x-icon name="users" class="w-4.5 h-4.5 text-blue-400" />
                        </div>
                        <div class="text-sm font-medium text-zinc-200">Participantes</div>
                        <div class="text-xs text-zinc-500 mt-0.5">Gestão completa</div>
                    </div>
                    <div class="card p-4">
                        <div class="w-9 h-9 rounded-lg bg-amber-500/10 flex items-center justify-center mb-3">
                            <x-icon name="file-text" class="w-4.5 h-4.5 text-amber-400" />
                        </div>
                        <div class="text-sm font-medium text-zinc-200">Relatórios</div>
                        <div class="text-xs text-zinc-500 mt-0.5">Análises detalhadas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.auth>