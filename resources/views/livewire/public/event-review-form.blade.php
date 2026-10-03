<section class="card p-6" id="avaliacoes">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-zinc-100">Avaliações</h2>
        @if($total > 0)
            <span class="badge badge-primary">★ {{ $average }} ({{ $total }})</span>
        @endif
    </div>

    @if($reviews->count() > 0)
        <div class="space-y-3 mb-6">
            @foreach($reviews as $review)
                <div class="p-4 rounded-lg bg-zinc-900/50 border border-zinc-800">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-zinc-100">{{ $review->name }}</span>
                        <span class="text-amber-400 text-sm">{{ str_repeat('★', $review->rating) }}</span>
                    </div>
                    @if($review->title)<div class="text-sm text-zinc-300 font-medium">{{ $review->title }}</div>@endif
                    @if($review->comment)<p class="text-sm text-zinc-400 mt-1">{{ $review->comment }}</p>@endif
                </div>
            @endforeach
        </div>
    @endif

    @if($sent)
        <div class="p-4 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-300">Obrigado! Sua avaliação foi registrada.</div>
    @else
        <form wire:submit.prevent="submit" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input type="text" wire:model="name" placeholder="Seu nome *" class="input">
                <input type="email" wire:model="email" placeholder="Seu e-mail (opcional)" class="input">
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-zinc-400">Nota:</span>
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" wire:click="$set('rating', {{ $i }})" class="text-2xl {{ $i <= $rating ? 'text-amber-400' : 'text-zinc-700' }}">★</button>
                @endfor
            </div>
            <input type="text" wire:model="title" placeholder="Resumo (opcional)" class="input">
            <textarea wire:model="comment" rows="3" placeholder="Como foi sua experiência?" class="input"></textarea>
            @error('name')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
            @error('rating')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
            <button type="submit" class="btn btn-primary">Enviar avaliação</button>
        </form>
    @endif
</section>
