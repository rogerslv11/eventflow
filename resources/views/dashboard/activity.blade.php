<x-layouts.app>
    <div class="space-y-6">
        <x-page-header title="Atividade" subtitle="Histórico de ações realizadas na plataforma." eyebrow="Auditoria" />

        <div class="card overflow-hidden">
            <div class="divide-y divide-zinc-800">
                @forelse($logs as $log)
                    <div class="px-5 py-3 flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0
                            @switch($log->action)
                                @case('created') bg-emerald-500/10 text-emerald-400 @break
                                @case('login') bg-blue-500/10 text-blue-400 @break
                                @case('check_in') bg-violet-500/10 text-violet-400 @break
                                @case('purchase') bg-amber-500/10 text-amber-400 @break
                                @default bg-zinc-800 text-zinc-400
                            @endswitch">
                            <x-icon :name="match($log->action) {
                                'created' => 'plus',
                                'login' => 'log-in',
                                'check_in' => 'check-circle',
                                'purchase' => 'shopping-cart',
                                'logout' => 'log-out',
                                'deleted' => 'trash',
                                'updated' => 'edit',
                                default => 'bell',
                            }" class="w-3.5 h-3.5" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-sm text-zinc-200">
                                    {{ $log->user?->name ?? 'Sistema' }}
                                    <span class="text-zinc-500">—</span>
                                    <span class="text-zinc-400">{{ $log->description ?? ucfirst($log->action) }}</span>
                                </div>
                                <div class="text-xs text-zinc-500 flex-shrink-0">
                                    {{ $log->performed_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="text-xs text-zinc-500 mt-1 flex items-center gap-3">
                                <span>{{ $log->ip_address ?? '—' }}</span>
                                @if($log->entity_type)
                                    <span>• {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center">
                        <p class="text-sm text-zinc-500">Nenhuma atividade registrada.</p>
                    </div>
                @endforelse
            </div>

            @if(method_exists($logs, 'hasPages') && $logs->hasPages())
                <div class="px-5 py-3 border-t border-zinc-800">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>