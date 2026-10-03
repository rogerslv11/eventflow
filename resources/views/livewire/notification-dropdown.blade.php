<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" class="relative p-2 hover:bg-zinc-900 rounded-lg transition-colors">
        <x-icon name="bell" class="w-5 h-5 text-zinc-400" />
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 w-2 h-2 bg-violet-500 rounded-full animate-pulse"></span>
            <span class="absolute -top-0.5 -right-0.5 min-w-4 h-4 px-1 bg-violet-500 text-white text-[10px] font-semibold rounded-full flex items-center justify-center">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak
         x-transition.origin.top.right
         @click.outside="open = false"
         class="absolute right-0 mt-2 w-96 bg-zinc-900 border border-zinc-800 rounded-xl shadow-2xl overflow-hidden z-50">

        <div class="px-4 py-3 border-b border-zinc-800 flex items-center justify-between">
            <h3 class="text-sm font-medium text-zinc-100">Notificações</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-violet-400 hover:text-violet-300">
                    Marcar todas como lidas
                </button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $n)
                <button wire:click="markAsRead({{ $n->id }})"
                        class="w-full px-4 py-3 flex items-start gap-3 hover:bg-zinc-800 transition-colors text-left border-b border-zinc-800 last:border-b-0
                            {{ $n->isRead() ? 'opacity-60' : '' }}">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ $n->color === 'success' ? 'bg-emerald-500/10 text-emerald-400' : '' }}
                        {{ $n->color === 'danger' ? 'bg-red-500/10 text-red-400' : '' }}
                        {{ $n->color === 'warning' ? 'bg-amber-500/10 text-amber-400' : '' }}
                        {{ $n->color === 'primary' || !$n->color ? 'bg-violet-500/10 text-violet-400' : '' }}">
                        <x-icon :name="$n->icon ?? 'bell'" class="w-4 h-4" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium text-zinc-100 truncate">{{ $n->title }}</p>
                            @if(!$n->isRead())
                                <span class="w-1.5 h-1.5 bg-violet-500 rounded-full flex-shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-xs text-zinc-400 mt-0.5 line-clamp-2">{{ $n->message }}</p>
                        <p class="text-xs text-zinc-500 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                    </div>
                </button>
            @empty
                <div class="px-4 py-12 text-center">
                    <x-icon name="bell" class="w-8 h-8 text-zinc-700 mx-auto mb-2" />
                    <p class="text-sm text-zinc-500">Nenhuma notificação</p>
                </div>
            @endforelse
        </div>

        <div class="px-4 py-2 border-t border-zinc-800">
            <a href="{{ route('dashboard.notifications.index') }}" class="block text-center text-xs text-violet-400 hover:text-violet-300 py-1.5">
                Ver todas
            </a>
        </div>
    </div>
</div>