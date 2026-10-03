<div x-data="{ items: [] }"
     x-init="$store.toasts.items.forEach(t => items.push(t)); $watch('$store.toasts.items', value => items = value)"
     class="fixed top-4 right-4 z-[100] flex flex-col gap-2 pointer-events-none">
    <template x-for="toast in items" :key="toast.id">
        <div x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-4"
             @click="$store.toasts.dismiss(toast.id)"
             class="pointer-events-auto cursor-pointer w-80 rounded-xl border bg-[#141419]/95 p-4 shadow-2xl backdrop-blur flex items-start gap-3"
             :class="{
                'border-emerald-500/30': toast.type === 'success',
                'border-red-500/30': toast.type === 'error',
                'border-yellow-500/30': toast.type === 'warning',
                'border-violet-500/30': toast.type === 'info' || !toast.type,
             }">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center"
                 :class="{
                    'bg-emerald-500/10 text-emerald-400': toast.type === 'success',
                    'bg-red-500/10 text-red-400': toast.type === 'error',
                    'bg-yellow-500/10 text-yellow-400': toast.type === 'warning',
                    'bg-violet-500/10 text-violet-400': toast.type === 'info' || !toast.type,
                 }">
                <template x-if="toast.type === 'success'">
                    <x-icon name="check-circle" class="w-5 h-5" />
                </template>
                <template x-if="toast.type === 'error'">
                    <x-icon name="x-circle" class="w-5 h-5" />
                </template>
                <template x-if="toast.type === 'warning'">
                    <x-icon name="alert-triangle" class="w-5 h-5" />
                </template>
                <template x-if="toast.type === 'info' || !toast.type">
                    <x-icon name="bell" class="w-5 h-5" />
                </template>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-zinc-100" x-text="toast.title"></div>
                <div class="text-xs text-zinc-400 mt-0.5" x-text="toast.message"></div>
            </div>
            <button @click.stop="$store.toasts.dismiss(toast.id)" class="text-zinc-500 hover:text-zinc-300">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>
    </template>
</div>