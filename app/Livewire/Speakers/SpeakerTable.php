<?php

namespace App\Livewire\Speakers;

use App\Models\Speaker;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Palestrantes')]
#[Layout('layouts.app')]
class SpeakerTable extends Component
{
    use WithFileUploads, WithPagination;

    #[Url]
    public string $search = '';

    public $avatar;

    public string $name = '';

    public string $bio = '';

    public string $role = '';

    public string $company = '';

    public string $email = '';

    public string $phone = '';

    public string $website = '';

    public string $linkedin = '';

    public string $twitter = '';

    public bool $is_featured = false;

    public ?int $editingId = null;

    public bool $showForm = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openForm(?Speaker $speaker = null): void
    {
        if ($speaker) {
            $this->editingId = $speaker->id;
            $this->name = $speaker->name;
            $this->bio = $speaker->bio ?? '';
            $this->role = $speaker->role ?? '';
            $this->company = $speaker->company ?? '';
            $this->email = $speaker->email ?? '';
            $this->phone = $speaker->phone ?? '';
            $this->website = $speaker->website ?? '';
            $social = $speaker->social_links ?? [];
            $this->linkedin = $social['linkedin'] ?? '';
            $this->twitter = $social['twitter'] ?? '';
            $this->is_featured = $speaker->is_featured;
        } else {
            $this->reset(['name', 'bio', 'role', 'company', 'email', 'phone', 'website', 'linkedin', 'twitter', 'is_featured', 'editingId', 'avatar']);
        }
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->reset(['name', 'bio', 'role', 'company', 'email', 'phone', 'website', 'linkedin', 'twitter', 'is_featured', 'editingId', 'avatar']);
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'role' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'is_featured' => 'boolean',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $orgId = Auth::user()->organizations()->first()?->id;

        $payload = [
            'organization_id' => $orgId,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::random(4),
            'bio' => $data['bio'] ?? null,
            'role' => $data['role'] ?? null,
            'company' => $data['company'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'website' => $data['website'] ?? null,
            'social_links' => array_filter([
                'linkedin' => $data['linkedin'] ?? null,
                'twitter' => $data['twitter'] ?? null,
            ]),
            'is_featured' => $data['is_featured'] ?? false,
        ];

        if (! empty($data['avatar'])) {
            $payload['avatar_path'] = $data['avatar']->store('speakers', 'public');
        }

        if ($this->editingId) {
            Speaker::find($this->editingId)->update($payload);
            ActivityLogger::log('updated', Speaker::find($this->editingId), "Palestrante atualizado: {$data['name']}");
            $this->dispatch('toast', type: 'success', title: 'Atualizado', message: 'Palestrante atualizado.');
        } else {
            $speaker = Speaker::create($payload);
            ActivityLogger::log('created', $speaker, "Palestrante criado: {$data['name']}");
            $this->dispatch('toast', type: 'success', title: 'Criado', message: 'Palestrante adicionado.');
        }

        $this->closeForm();
    }

    public function delete(Speaker $speaker): void
    {
        ActivityLogger::log('deleted', $speaker, "Palestrante excluído: {$speaker->name}");
        $speaker->delete();
        $this->dispatch('toast', type: 'success', title: 'Excluído', message: 'Palestrante removido.');
    }

    public function toggleFeatured(Speaker $speaker): void
    {
        $speaker->update(['is_featured' => ! $speaker->is_featured]);
    }

    public function render()
    {
        $orgId = Auth::user()->organizations()->first()?->id;
        $speakers = Speaker::query()
            ->where('organization_id', $orgId)
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('company', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(15);

        return view('livewire.speakers.speaker-table', [
            'speakers' => $speakers,
        ]);
    }
}
