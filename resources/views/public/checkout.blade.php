<x-layouts.public>
    <header class="sticky top-0 z-40 border-b border-zinc-800/80 bg-zinc-950/85 backdrop-blur-xl">
        <div class="mx-auto flex h-16 max-w-4xl items-center justify-between px-4 lg:px-6">
            <a href="{{ route('public.event.show', $event->slug) }}" class="btn btn-ghost btn-sm">
                <x-icon name="chevron-left" class="h-4 w-4" />
                <span>Voltar para o evento</span>
            </a>
            <div class="flex items-center gap-2">
                <div class="relative flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-violet-400 via-violet-600 to-violet-800 shadow-[0_4px_16px_-4px_rgb(124_58_237/0.7)]">
                    <x-icon name="sparkles" class="h-4 w-4 text-white" />
                    <div class="absolute inset-0 rounded-xl ring-1 ring-inset ring-white/20"></div>
                </div>
                <span class="text-[0.9375rem] font-semibold tracking-tight text-zinc-50">EventFlow</span>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 lg:px-6 py-8">
        <div class="eyebrow mb-2">
            <span class="h-1.5 w-1.5 rounded-full bg-violet-400"></span>
            Checkout seguro
        </div>
        <h1 class="page-title mb-1">Finalizar compra</h1>
        <p class="page-subtitle mb-8">{{ $event->name }}</p>

        <div x-data="{
                selectedItems: {},
                customer: { name: '', email: '', document: '', phone: '', company: '', role: '' },
                coupon: '',
                get total() {
                    return Object.values(this.selectedItems).reduce((sum, item) => {
                        return sum + (item.price * item.quantity);
                    }, 0);
                },
                get totalQuantity() {
                    return Object.values(this.selectedItems).reduce((sum, item) => sum + item.quantity, 0);
                },
                increment(ticketTypeId, price) {
                    if (!this.selectedItems[ticketTypeId]) {
                        this.selectedItems[ticketTypeId] = { quantity: 0, price };
                    }
                    if (this.selectedItems[ticketTypeId].quantity < 10) {
                        this.selectedItems[ticketTypeId].quantity++;
                    }
                },
                decrement(ticketTypeId) {
                    if (this.selectedItems[ticketTypeId] && this.selectedItems[ticketTypeId].quantity > 0) {
                        this.selectedItems[ticketTypeId].quantity--;
                        if (this.selectedItems[ticketTypeId].quantity === 0) {
                            delete this.selectedItems[ticketTypeId];
                        }
                    }
                }
             }" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Form --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Tickets --}}
                <div class="card p-6">
                    <h3 class="text-base font-semibold text-zinc-100 mb-4">1. Escolha os ingressos</h3>
                    <div class="space-y-3">
                        @foreach($event->ticketTypes->where('is_active', true) as $tt)
                            <div class="p-4 rounded-lg border border-zinc-800 bg-zinc-900/30 flex items-center justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-zinc-100">{{ $tt->name }}</div>
                                    <div class="text-xs text-zinc-500 mt-0.5">{{ $tt->description }}</div>
                                    <div class="text-sm font-semibold gradient-text mt-2">{{ $tt->formattedPrice() }}</div>
                                </div>
                                <div class="flex items-center gap-1 bg-zinc-900 border border-zinc-800 rounded-lg">
                                    <button @click="decrement({{ $tt->id }}, {{ $tt->price }})" type="button" class="p-2 text-zinc-400 hover:text-zinc-100">
                                        <x-icon name="minus" class="w-4 h-4" />
                                    </button>
                                    <span class="w-8 text-center text-zinc-100 font-medium" x-text="selectedItems[{{ $tt->id }}]?.quantity || 0"></span>
                                    <button @click="increment({{ $tt->id }}, {{ $tt->price }})" type="button" class="p-2 text-zinc-400 hover:text-zinc-100">
                                        <x-icon name="plus" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Customer info --}}
                <div class="card p-6">
                    <h3 class="text-base font-semibold text-zinc-100 mb-4">2. Seus dados</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="label">Nome completo *</label>
                            <input type="text" x-model="customer.name" class="input" placeholder="João Silva">
                        </div>
                        <div>
                            <label class="label">E-mail *</label>
                            <input type="email" x-model="customer.email" class="input" placeholder="seu@email.com">
                        </div>
                        <div>
                            <label class="label">CPF</label>
                            <input type="text" x-model="customer.document" class="input" placeholder="000.000.000-00">
                        </div>
                        <div>
                            <label class="label">Telefone</label>
                            <input type="text" x-model="customer.phone" class="input" placeholder="(11) 99999-9999">
                        </div>
                        <div>
                            <label class="label">Empresa</label>
                            <input type="text" x-model="customer.company" class="input">
                        </div>
                        <div>
                            <label class="label">Cargo</label>
                            <input type="text" x-model="customer.role" class="input">
                        </div>
                    </div>
                </div>

                {{-- Coupon --}}
                <div class="card p-6">
                    <h3 class="text-base font-semibold text-zinc-100 mb-4">3. Cupom de desconto</h3>
                    <div class="flex gap-2">
                        <input type="text" x-model="coupon" class="input flex-1" placeholder="Digite seu cupom">
                        <button type="button" class="btn btn-secondary">
                            <x-icon name="check" class="w-4 h-4" />
                            Aplicar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Summary --}}
            <aside class="lg:col-span-1">
                <div class="card p-6 sticky top-20">
                    <h3 class="text-base font-semibold text-zinc-100 mb-4">Resumo</h3>

                    <div class="space-y-2 mb-4 pb-4 border-b border-zinc-800">
                        <template x-for="(item, id) in selectedItems" :key="id">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-zinc-400">
                                    Ingresso <span x-text="id"></span> × <span x-text="item.quantity"></span>
                                </span>
                                <span class="text-zinc-200">
                                    R$ <span x-text="(item.price * item.quantity).toFixed(2).replace('.', ',')"></span>
                                </span>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <span class="text-sm text-zinc-400">Total</span>
                        <span class="text-2xl font-semibold gradient-text">
                            R$ <span x-text="total.toFixed(2).replace('.', ',')"></span>
                        </span>
                    </div>

                    <button
                        type="button"
                        :disabled="totalQuantity === 0 || !customer.name || !customer.email"
                        @click="
                            $wire.call('processCheckout', selectedItems, customer, coupon).then(() => {
                                // will be handled by redirect
                            })
                        "
                        class="btn btn-primary w-full"
                        :class="{ 'opacity-50 cursor-not-allowed': totalQuantity === 0 || !customer.name || !customer.email }">
                        <x-icon name="credit-card" class="w-4 h-4" />
                        Pagar com cartão
                    </button>

                    <p class="text-xs text-zinc-500 text-center mt-4">
                        Ao continuar você concorda com os termos de uso.
                    </p>
                </div>
            </aside>
        </div>
    </main>
</x-layouts.public>