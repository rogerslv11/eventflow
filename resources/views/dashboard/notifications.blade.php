<x-layouts.app>
    <div class="space-y-6">
        <x-page-header title="Notificações" subtitle="Central de notificações da sua conta." eyebrow="Conta" />

        <div class="card overflow-hidden">
            @forelse($notifications as $n)
                <div class="px-5 py-4 border-b border-zinc-800 flex items-start gap-3 hover:bg-zinc-900/30 {{ $n->isRead() ? 'opacity-60' : '' }}">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ $n->color === 'success' ? 'bg-emerald-500/10 text-emerald-400' : '' }}
                        {{ $n->color === 'danger' ? 'bg-red-500/10 text-red-400' : '' }}
                        {{ $n->color === 'warning' ? 'bg-amber-500/10 text-amber-400' : '' }}
                        {{ $n->color === 'primary' || !$n->color ? 'bg-violet-500/10 text-violet-400' : '' }}">
                        <x-icon :name="$n->icon ?? 'bell'" class="w-4 h-4" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium text-zinc-100">{{ $n->title }}</p>
                            @if(!$n->isRead())
                                <span class="w-1.5 h-1.5 bg-violet-500 rounded-full"></span>
                            @endif
                        </div>
                        <p class="text-sm text-zinc-400 mt-1">{{ $n->message }}</p>
                        <p class="text-xs text-zinc-500 mt-2">{{ $n->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center">
                    <x-icon name="bell" class="w-12 h-12 text-zinc-700 mx-auto mb-3" />
                    <p class="text-sm text-zinc-500">Nenhuma notificação ainda.</p>
                </div>
            @endforelse

            @if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
                <div class="px-5 py-3 border-t border-zinc-800">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>