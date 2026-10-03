<x-layouts.auth>
    <div class="min-h-screen flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-md">
            <div class="mb-10 text-center">
                <a href="/" class="inline-flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-violet-700 flex items-center justify-center">
                        <x-icon name="sparkles" class="w-5 h-5 text-white" />
                    </div>
                    <span class="text-xl font-semibold text-zinc-100">EventFlow</span>
                </a>
            </div>

            <div class="card p-8">
                <h1 class="text-2xl font-semibold text-zinc-100 mb-2">Redefinir senha</h1>
                <p class="text-sm text-zinc-400 mb-6">Escolha uma nova senha para sua conta.</p>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-sm text-red-300">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label class="label">E-mail</label>
                        <input type="email" name="email" required class="input" placeholder="seu@email.com">
                    </div>

                    <div>
                        <label class="label">Nova senha</label>
                        <input type="password" name="password" required class="input" placeholder="••••••••">
                    </div>

                    <div>
                        <label class="label">Confirmar nova senha</label>
                        <input type="password" name="password_confirmation" required class="input" placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        <x-icon name="lock" class="w-4 h-4" />
                        Redefinir senha
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.auth>