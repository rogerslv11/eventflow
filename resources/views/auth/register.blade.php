<x-layouts.auth>
    <div class="min-h-screen flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-md">
            {{-- Logo --}}
            <div class="mb-10 text-center">
                <a href="/" class="inline-flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-violet-700 flex items-center justify-center">
                        <x-icon name="sparkles" class="w-5 h-5 text-white" />
                    </div>
                    <span class="text-xl font-semibold text-zinc-100">EventFlow</span>
                </a>
            </div>

            {{-- Card --}}
            <div class="card p-8">
                <h1 class="text-2xl font-semibold text-zinc-100 mb-2">Criar nova conta</h1>
                <p class="text-sm text-zinc-400 mb-6">Comece a gerenciar seus eventos hoje mesmo.</p>

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

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="label">Seu nome</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus class="input" placeholder="João Silva">
                    </div>

                    <div>
                        <label class="label">Nome da organização</label>
                        <input type="text" name="organization_name" value="{{ old('organization_name') }}" required class="input" placeholder="Minha Empresa de Eventos">
                    </div>

                    <div>
                        <label class="label">E-mail</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="input" placeholder="seu@email.com">
                    </div>

                    <div>
                        <label class="label">Senha</label>
                        <input type="password" name="password" required class="input" placeholder="••••••••">
                    </div>

                    <div>
                        <label class="label">Confirmar senha</label>
                        <input type="password" name="password_confirmation" required class="input" placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        <x-icon name="user-plus" class="w-4 h-4" />
                        Criar conta
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-zinc-400">
                    Já tem uma conta?
                    <a href="{{ route('login') }}" class="text-violet-400 hover:text-violet-300 font-medium">
                        Entrar
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.auth>