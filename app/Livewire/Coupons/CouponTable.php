<?php

namespace App\Livewire\Coupons;

use App\Enums\CouponType;
use App\Models\Coupon;
use App\Models\Event;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Cupons')]
#[Layout('layouts.app')]
class CouponTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterEvent = '';

    public string $code = '';

    public string $name = '';

    public string $description = '';

    public string $type = 'percentage';

    public string $value = '';

    public ?int $usage_limit = null;

    public int $usage_limit_per_user = 1;

    public ?string $min_purchase = null;

    public ?string $max_discount = null;

    public string $starts_at = '';

    public string $ends_at = '';

    public bool $is_active = true;

    public ?int $event_id = null;

    public ?int $editingId = null;

    public bool $showForm = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterEvent(): void
    {
        $this->resetPage();
    }

    public function openForm(?Coupon $coupon = null): void
    {
        if ($coupon) {
            $this->editingId = $coupon->id;
            $this->code = $coupon->code;
            $this->name = $coupon->name;
            $this->description = $coupon->description ?? '';
            $this->type = $coupon->type->value;
            $this->value = (string) $coupon->value;
            $this->usage_limit = $coupon->usage_limit;
            $this->usage_limit_per_user = $coupon->usage_limit_per_user;
            $this->min_purchase = $coupon->min_purchase;
            $this->max_discount = $coupon->max_discount;
            $this->starts_at = $coupon->starts_at?->format('Y-m-d\TH:i') ?? '';
            $this->ends_at = $coupon->ends_at?->format('Y-m-d\TH:i') ?? '';
            $this->is_active = $coupon->is_active;
            $this->event_id = $coupon->event_id;
        } else {
            $this->reset(['code', 'name', 'description', 'type', 'value', 'usage_limit', 'usage_limit_per_user', 'min_purchase', 'max_discount', 'starts_at', 'ends_at', 'event_id', 'editingId']);
            $this->type = 'percentage';
            $this->usage_limit_per_user = 1;
            $this->is_active = true;
            $this->starts_at = now()->format('Y-m-d\TH:i');
            $this->ends_at = now()->addDays(60)->format('Y-m-d\TH:i');
        }
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->reset(['code', 'name', 'description', 'type', 'value', 'usage_limit', 'usage_limit_per_user', 'min_purchase', 'max_discount', 'starts_at', 'ends_at', 'event_id', 'editingId']);
    }

    public function generateCode(): void
    {
        $this->code = strtoupper(Str::random(8));
    }

    public function save(): void
    {
        $data = $this->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($this->editingId)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(array_column(CouponType::cases(), 'value'))],
            'value' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'required|integer|min:1',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
            'event_id' => 'nullable|exists:events,id',
        ]);

        $data['organization_id'] = Auth::user()->organizations()->first()?->id;
        $data['starts_at'] = $data['starts_at'] ?: null;
        $data['ends_at'] = $data['ends_at'] ?: null;
        $data['code'] = strtoupper($data['code']);
        $data['value'] = $data['value'];
        $data['usage_count'] = 0;

        if ($this->editingId) {
            Coupon::find($this->editingId)->update($data);
            ActivityLogger::log('updated', Coupon::find($this->editingId), "Cupom atualizado: {$data['code']}");
            $this->dispatch('toast', type: 'success', title: 'Atualizado', message: 'Cupom atualizado.');
        } else {
            $coupon = Coupon::create($data);
            ActivityLogger::log('created', $coupon, "Cupom criado: {$coupon->code}");
            $this->dispatch('toast', type: 'success', title: 'Criado', message: 'Cupom criado.');
        }

        $this->closeForm();
    }

    public function toggleActive(Coupon $coupon): void
    {
        $coupon->update(['is_active' => ! $coupon->is_active]);
        $this->dispatch('toast', type: 'success', title: 'Atualizado', message: 'Status alterado.');
    }

    public function delete(Coupon $coupon): void
    {
        ActivityLogger::log('deleted', $coupon, "Cupom excluído: {$coupon->code}");
        $coupon->delete();
        $this->dispatch('toast', type: 'success', title: 'Excluído', message: 'Cupom removido.');
    }

    public function render()
    {
        $orgId = Auth::user()->organizations()->first()?->id;
        $coupons = Coupon::query()
            ->with('event')
            ->where('organization_id', $orgId)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('code', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%");
            }))
            ->when($this->filterEvent, fn ($q) => $q->where('event_id', $this->filterEvent))
            ->latest()
            ->paginate(15);

        $events = Event::where('organization_id', $orgId)->orderBy('name')->get(['id', 'name']);

        return view('livewire.coupons.coupon-table', [
            'coupons' => $coupons,
            'events' => $events,
        ]);
    }
}
