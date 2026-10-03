<div x-show="$store.commandPalette.open"
     x-cloak
     @keydown.escape.window="$store.commandPalette.close()"
     class="fixed inset-0 z-[100] flex items-start justify-center pt-24 px-4">

    {{-- Backdrop --}}
    <div x-transition.opacity
         @click="$store.commandPalette.close()"
         class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

    {{-- Palette --}}
    <div x-data="{
            query: '',
            results: [],
            selectedIndex: 0,
            search() {
                if (this.query.length < 2) { this.results = []; return; }
                fetch(`/api/search?q=${encodeURIComponent(this.query)}`)
                    .then(r => r.json())
                    .then(data => this.results = data);
            },
            select(item) {
                window.location.href = item.url;
            }
         }"
         x-init="$watch('query', () => search())"
         @keydown.down.prevent="selectedIndex = Math.min(selectedIndex + 1, results.flatMap(r => r.items).length - 1)"
         @keydown.up.prevent="selectedIndex = Math.max(selectedIndex - 1, 0)"
         @keydown.enter.prevent="
            const items = results.flatMap(r => r.items);
            if (items[selectedIndex]) select(items[selectedIndex]);
         "
         x-transition
         class="modal-panel max-w-2xl overflow-hidden animate-modal-in">

        {{-- Input --}}
        <div class="flex items-center gap-3 px-4 py-4 border-b border-zinc-800">
            <x-icon name="search" class="w-5 h-5 text-zinc-500" />
            <input id="command-palette-input"
                   type="text"
                   x-model="query"
                   placeholder="Buscar eventos, participantes, pedidos..."
                   class="flex-1 bg-transparent border-0 outline-none text-zinc-100 placeholder-zinc-500 text-sm"
                   autocomplete="off">
            <kbd class="text-xs text-zinc-500 bg-zinc-800 px-1.5 py-0.5 rounded">ESC</kbd>
        </div>

        {{-- Results --}}
        <div class="max-h-96 overflow-y-auto">
            <template x-if="query.length < 2">
                <div class="px-4 py-8 text-center text-sm text-zinc-500">
                    Digite pelo menos 2 caracteres para buscar
                </div>
            </template>

            <template x-if="query.length >= 2 && results.length === 0">
                <div class="px-4 py-8 text-center text-sm text-zinc-500">
                    Nenhum resultado encontrado para "<span x-text="query"></span>"
                </div>
            </template>

            <template x-for="(group, gi) in results" :key="gi">
                <div class="border-b border-zinc-800 last:border-b-0">
                    <div class="px-4 py-2 text-xs font-medium text-zinc-500 uppercase tracking-wider" x-text="group.label"></div>
                    <template x-for="(item, i) in group.items" :key="i">
                        <button @click="select(item)"
                                :class="{
                                    'bg-violet-500/10 border-l-2 border-violet-500': selectedIndex === i,
                                    'border-l-2 border-transparent': selectedIndex !== i,
                                }"
                                class="w-full px-4 py-2.5 flex items-center gap-3 text-left hover:bg-zinc-800 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-400">
                                <x-icon name="search" class="w-4 h-4" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-zinc-100 truncate" x-text="item.title"></div>
                                <div class="text-xs text-zinc-500 truncate" x-text="item.subtitle"></div>
                            </div>
                            <x-icon name="arrow-up-right" class="w-4 h-4 text-zinc-500" />
                        </button>
                    </template>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="px-4 py-2.5 border-t border-zinc-800 flex items-center justify-between text-xs text-zinc-500">
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-1"><kbd class="bg-zinc-800 px-1.5 py-0.5 rounded">↑↓</kbd> navegar</span>
                <span class="flex items-center gap-1"><kbd class="bg-zinc-800 px-1.5 py-0.5 rounded">↵</kbd> selecionar</span>
                <span class="flex items-center gap-1"><kbd class="bg-zinc-800 px-1.5 py-0.5 rounded">ESC</kbd> fechar</span>
            </div>
            <span>EventFlow</span>
        </div>
    </div>
</div>