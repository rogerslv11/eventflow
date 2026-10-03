<div id="waitlist" class="pt-4 border-t border-zinc-800">
    <h4 class="text-sm font-semibold text-zinc-100 mb-1">Esgotado? Entre na lista de espera</h4>
    <p class="text-sm text-zinc-500 mb-4">{{ $totalWaiting }} pessoa(s) aguardando • Avisamos quando liberar vaga.</p>
    @if($joined)
        <div class="p-4 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-300">Você está na lista! Avisaremos por e-mail.</div>
    @else
        <form wire:submit.prevent="submit" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input type="text" wire:model="name" placeholder="Seu nome *" class="input">
                <input type="email" wire:model="email" placeholder="Seu e-mail *" class="input">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input type="text" wire:model="phone" placeholder="WhatsApp (opcional)" class="input">
                <input type="number" min="1" max="10" wire:model="quantity" placeholder="Qtd" class="input">
            </div>
            @error('name')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
            @error('email')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
            <button type="submit" class="btn btn-secondary w-full">Avise-me quando liberar</button>
        </form>
    @endif
</div>
