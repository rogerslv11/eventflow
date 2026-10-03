<x-layouts.app>
    <div class="space-y-6" x-data="{ tab: 'profile' }">
        <x-page-header title="Configurações" subtitle="Gerencie sua conta, organização e aparência." eyebrow="Sistema">
            <x-slot:actions>
                <span class="badge badge-neutral">
                    <x-icon name="lock" class="w-3 h-3" />
                    Salvamento automático
                </span>
            </x-slot:actions>
        </x-page-header>

        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center gap-3 animate-fade-in">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                    <x-icon name="check-circle" class="w-4 h-4 text-emerald-400" />
                </div>
                <p class="text-sm text-emerald-300 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                <nav class="card p-2 space-y-0.5 sticky top-20">
                    <div class="px-3 py-2 text-xs font-medium text-zinc-500 uppercase tracking-wider">Conta</div>

                    <button @click="tab = 'profile'" :class="tab === 'profile' ? 'bg-violet-500/10 text-violet-300 border-violet-500/30' : 'text-zinc-300 hover:bg-zinc-800 border-transparent'"
                            class="w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg text-left transition-colors border">
                        <x-icon name="user-check" class="w-4 h-4" />
                        <div class="flex-1">
                            <div class="font-medium">Perfil</div>
                            <div class="text-xs text-zinc-500">Suas informações pessoais</div>
                        </div>
                    </button>

                    <button @click="tab = 'password'" :class="tab === 'password' ? 'bg-violet-500/10 text-violet-300 border-violet-500/30' : 'text-zinc-300 hover:bg-zinc-800 border-transparent'"
                            class="w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg text-left transition-colors border">
                        <x-icon name="lock" class="w-4 h-4" />
                        <div class="flex-1">
                            <div class="font-medium">Senha</div>
                            <div class="text-xs text-zinc-500">Alterar senha</div>
                        </div>
                    </button>

                    @if($organization)
                        <div class="px-3 py-2 mt-2 text-xs font-medium text-zinc-500 uppercase tracking-wider">Organização</div>

                        <button @click="tab = 'organization'" :class="tab === 'organization' ? 'bg-violet-500/10 text-violet-300 border-violet-500/30' : 'text-zinc-300 hover:bg-zinc-800 border-transparent'"
                                class="w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg text-left transition-colors border">
                            <x-icon name="building" class="w-4 h-4" />
                            <div class="flex-1">
                                <div class="font-medium">Dados</div>
                                <div class="text-xs text-zinc-500">{{ $organization->name }}</div>
                            </div>
                        </button>

                        <button @click="tab = 'appearance'" :class="tab === 'appearance' ? 'bg-violet-500/10 text-violet-300 border-violet-500/30' : 'text-zinc-300 hover:bg-zinc-800 border-transparent'"
                                class="w-full flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg text-left transition-colors border">
                            <x-icon name="sparkles" class="w-4 h-4" />
                            <div class="flex-1">
                                <div class="font-medium">Aparência</div>
                                <div class="text-xs text-zinc-500">Cores e marca</div>
                            </div>
                        </button>
                    @endif
                </nav>
            </div>

            {{-- Content --}}
            <div class="lg:col-span-3 space-y-6">
                {{-- Profile --}}
                <div x-show="tab === 'profile'" x-cloak class="card p-6">
                    <div class="flex items-center gap-4 pb-6 border-b border-zinc-800 mb-6">
                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full border-2 border-zinc-800">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-zinc-100">Informações do perfil</h3>
                            <p class="text-sm text-zinc-500 mt-0.5">Atualize suas informações pessoais.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('dashboard.settings.profile') }}" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="label">Nome completo</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input" required>
                                @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="label">E-mail</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input" required>
                                @error('email') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="label">Telefone</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input" placeholder="+55 11 99999-9999">
                            </div>
                            <div>
                                <label class="label">Documento (CPF/CNPJ)</label>
                                <input type="text" name="document" value="{{ old('document', $user->document) }}" class="input" placeholder="000.000.000-00">
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-4 border-t border-zinc-800">
                            <p class="text-xs text-zinc-500">Última atualização: {{ $user->updated_at->diffForHumans() }}</p>
                            <button type="submit" class="btn btn-primary">
                                <x-icon name="check" class="w-4 h-4" />
                                Salvar alterações
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Password --}}
                <div x-show="tab === 'password'" x-cloak class="card p-6">
                    <div class="flex items-center gap-4 pb-6 border-b border-zinc-800 mb-6">
                        <div class="w-16 h-16 rounded-full bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                            <x-icon name="lock" class="w-7 h-7 text-amber-400" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-zinc-100">Alterar senha</h3>
                            <p class="text-sm text-zinc-500 mt-0.5">Mantenha sua conta segura com uma senha forte.</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('dashboard.settings.password') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label class="label">Senha atual</label>
                            <input type="password" name="current_password" class="input" required>
                            @error('current_password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="label">Nova senha</label>
                                <input type="password" name="password" class="input" required minlength="8">
                                <p class="text-xs text-zinc-500 mt-1.5">Mínimo de 8 caracteres.</p>
                            </div>
                            <div>
                                <label class="label">Confirmar nova senha</label>
                                <input type="password" name="password_confirmation" class="input" required>
                            </div>
                        </div>
                        <div class="flex items-center justify-end pt-4 border-t border-zinc-800">
                            <button type="submit" class="btn btn-primary">
                                <x-icon name="lock" class="w-4 h-4" />
                                Atualizar senha
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Organization --}}
                @if($organization)
                    <div x-show="tab === 'organization'" x-cloak class="card p-6">
                        <div class="flex items-center gap-4 pb-6 border-b border-zinc-800 mb-6">
                            <img src="{{ $organization->logoUrl() }}" alt="{{ $organization->name }}" class="w-16 h-16 rounded-xl border-2 border-zinc-800">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-zinc-100">Dados da organização</h3>
                                <p class="text-sm text-zinc-500 mt-0.5">Informações institucionais.</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('dashboard.settings.organization') }}" class="space-y-5">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="label">Nome da organização</label>
                                    <input type="text" name="name" value="{{ old('name', $organization->name) }}" class="input" required>
                                </div>
                                <div>
                                    <label class="label">E-mail</label>
                                    <input type="email" name="email" value="{{ old('email', $organization->email) }}" class="input">
                                </div>
                                <div>
                                    <label class="label">Telefone</label>
                                    <input type="text" name="phone" value="{{ old('phone', $organization->phone) }}" class="input">
                                </div>
                                <div>
                                    <label class="label">Documento (CNPJ)</label>
                                    <input type="text" name="document" value="{{ old('document', $organization->document) }}" class="input">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="label">Endereço</label>
                                    <input type="text" name="address" value="{{ old('address', $organization->address) }}" class="input">
                                </div>
                            </div>
                            <div class="flex items-center justify-end pt-4 border-t border-zinc-800">
                                <button type="submit" class="btn btn-primary">
                                    <x-icon name="check" class="w-4 h-4" />
                                    Salvar organização
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Appearance --}}
                    <div x-show="tab === 'appearance'" x-cloak class="card p-6">
                        <div class="flex items-center gap-4 pb-6 border-b border-zinc-800 mb-6">
                            <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-violet-500 to-violet-700 flex items-center justify-center">
                                <x-icon name="sparkles" class="w-7 h-7 text-white" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-zinc-100">Aparência</h3>
                                <p class="text-sm text-zinc-500 mt-0.5">Personalize as cores da sua marca.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('dashboard.settings.organization') }}" class="space-y-5">
                            @csrf
                            <div>
                                <label class="label">Cor principal</label>
                                <div class="flex items-center gap-4">
                                    <input type="color" name="primary_color" value="{{ old('primary_color', $organization->primary_color) }}" class="w-16 h-10 rounded-lg border border-zinc-700 cursor-pointer">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            @foreach(['#7C3AED', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#EC4899'] as $color)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="primary_color_preset" value="{{ $color }}" class="sr-only peer" {{ $organization->primary_color === $color ? 'checked' : '' }}>
                                                    <div class="w-8 h-8 rounded-lg border-2 border-transparent peer-checked:border-white transition-all hover:scale-110" style="background-color: {{ $color }}"></div>
                                                </label>
                                            @endforeach
                                        </div>
                                        <p class="text-xs text-zinc-500">Escolha uma cor predefinida ou personalize.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-zinc-800 flex items-center justify-between">
                                <p class="text-xs text-zinc-500">Cor atual: <code class="text-violet-300">{{ $organization->primary_color }}</code></p>
                                <button type="submit" class="btn btn-primary">
                                    <x-icon name="check" class="w-4 h-4" />
                                    Salvar aparência
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>