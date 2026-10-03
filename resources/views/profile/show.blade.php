<x-layouts.app>
    <div class="space-y-6">
        <x-page-header title="Meu perfil" subtitle="Gerencie suas informações pessoais." eyebrow="Conta" />

        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Avatar card --}}
            <div class="card p-6 text-center">
                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full mx-auto mb-4 border-2 border-zinc-800">
                <h3 class="text-lg font-semibold text-zinc-100">{{ $user->name }}</h3>
                <p class="text-sm text-zinc-500">{{ $user->email }}</p>
                <p class="text-xs text-zinc-600 mt-2">
                    Membro desde {{ $user->created_at->format('d/m/Y') }}
                </p>

                @if($organizations->count() > 0)
                    <div class="mt-6 pt-6 border-t border-zinc-800 text-left">
                        <h4 class="text-xs uppercase tracking-wider text-zinc-500 mb-3">Organizações</h4>
                        @foreach($organizations as $org)
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-7 h-7 rounded-md bg-violet-500/10 flex items-center justify-center text-violet-300 text-xs font-semibold">
                                    {{ mb_strtoupper(mb_substr($org->name, 0, 1)) }}
                                </div>
                                <div class="text-sm text-zinc-200">{{ $org->name }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Form --}}
            <div class="card p-6 lg:col-span-2">
                <h3 class="text-base font-semibold text-zinc-100 mb-4">Editar perfil</h3>
                <form method="POST" action="{{ route('dashboard.profile.update') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="label">Nome</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input" required>
                    </div>
                    <div>
                        <label class="label">E-mail</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input" required>
                    </div>
                    <div>
                        <label class="label">Telefone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input">
                    </div>
                    <div class="pt-4 border-t border-zinc-800">
                        <button type="submit" class="btn btn-primary">
                            <x-icon name="check" class="w-4 h-4" />
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>