<?php

namespace App\Livewire\Vendors;

use App\Enums\VendorCategory;
use App\Models\Vendor;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Fornecedores')]
#[Layout('layouts.app')]
class VendorTable extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterCategory = '';

    public string $name = '';

    public string $company_name = '';

    public string $email = '';

    public string $phone = '';

    public string $document = '';

    public string $category = 'other';

    public string $cost = '';

    public string $address = '';

    public string $notes = '';

    public string $status = 'active';

    public ?int $editingId = null;

    public bool $showForm = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function openForm(?Vendor $vendor = null): void
    {
        if ($vendor) {
            $this->editingId = $vendor->id;
            $this->fill($vendor->toArray());
            $this->category = $vendor->category->value;
        } else {
            $this->reset(['name', 'company_name', 'email', 'phone', 'document', 'category', 'cost', 'address', 'notes', 'status', 'editingId']);
            $this->category = 'other';
            $this->status = 'active';
        }
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->reset(['name', 'company_name', 'email', 'phone', 'document', 'category', 'cost', 'address', 'notes', 'status', 'editingId']);
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'document' => 'nullable|string|max:20',
            'category' => ['required', Rule::in(array_column(VendorCategory::cases(), 'value'))],
            'cost' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $data['organization_id'] = Auth::user()->organizations()->first()?->id;
        $data['cost'] = $data['cost'] ?: null;

        if ($this->editingId) {
            Vendor::find($this->editingId)->update($data);
            ActivityLogger::log('updated', Vendor::find($this->editingId), "Fornecedor atualizado: {$data['name']}");
            $this->dispatch('toast', type: 'success', title: 'Atualizado', message: 'Fornecedor atualizado.');
        } else {
            $vendor = Vendor::create($data);
            ActivityLogger::log('created', $vendor, "Fornecedor criado: {$data['name']}");
            $this->dispatch('toast', type: 'success', title: 'Criado', message: 'Fornecedor adicionado.');
        }

        $this->closeForm();
    }

    public function delete(Vendor $vendor): void
    {
        ActivityLogger::log('deleted', $vendor, "Fornecedor excluído: {$vendor->name}");
        $vendor->delete();
        $this->dispatch('toast', type: 'success', title: 'Excluído', message: 'Fornecedor removido.');
    }

    public function render()
    {
        $orgId = Auth::user()->organizations()->first()?->id;
        $vendors = Vendor::query()
            ->where('organization_id', $orgId)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('company_name', 'like', "%{$this->search}%");
            }))
            ->when($this->filterCategory, fn ($q) => $q->where('category', $this->filterCategory))
            ->latest()
            ->paginate(15);

        return view('livewire.vendors.vendor-table', [
            'vendors' => $vendors,
            'categories' => VendorCategory::cases(),
        ]);
    }
}
