<?php

namespace App\Livewire\Events;

use App\Enums\EventStatus;
use App\Models\Category;
use App\Models\Event;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Evento')]
#[Layout('layouts.app')]
class EventForm extends Component
{
    use WithFileUploads;

    public ?Event $event = null;

    // Form fields
    public string $name = '';

    public string $description = '';

    public string $content = '';

    public ?int $category_id = null;

    public string $venue_name = '';

    public string $address = '';

    public string $city = '';

    public string $state = '';

    public string $zipcode = '';

    public string $starts_at = '';

    public string $ends_at = '';

    public string $timezone = 'America/Sao_Paulo';

    public ?int $capacity = null;

    public string $status = 'draft';

    public bool $is_featured = false;

    public bool $is_public = true;

    public $image;

    public $banner;

    public function mount(?Event $event = null): void
    {
        if ($event && $event->exists) {
            $this->event = $event;
            $this->fill([
                'name' => $event->name,
                'description' => $event->description ?? '',
                'content' => $event->content ?? '',
                'category_id' => $event->category_id,
                'venue_name' => $event->venue_name ?? '',
                'address' => $event->address ?? '',
                'city' => $event->city ?? '',
                'state' => $event->state ?? '',
                'zipcode' => $event->zipcode ?? '',
                'starts_at' => $event->starts_at?->format('Y-m-d\TH:i') ?? '',
                'ends_at' => $event->ends_at?->format('Y-m-d\TH:i') ?? '',
                'capacity' => $event->capacity,
                'status' => $event->status->value,
                'is_featured' => $event->is_featured,
                'is_public' => $event->is_public,
            ]);
        }
    }

    #[Computed]
    public function categories()
    {
        $orgId = Auth::user()->organizations()->first()?->id;

        return Category::where('organization_id', $orgId)
            ->where('is_active', true)
            ->get();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:2'],
            'zipcode' => ['nullable', 'string', 'max:10'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', Rule::in(array_column(EventStatus::cases(), 'value'))],
            'is_featured' => ['boolean'],
            'is_public' => ['boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
            'banner' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        $organization = Auth::user()->organizations()->first();
        if (! $organization) {
            $this->dispatch('toast', type: 'error', title: 'Erro', message: 'Você precisa ter uma organização.');

            return;
        }

        $payload = collect($data)->except(['image', 'banner'])->toArray();

        if ($this->image) {
            $payload['image_path'] = $this->image->store('events/'.$organization->id, 'public');
        }
        if ($this->banner) {
            $payload['banner_path'] = $this->banner->store('events/'.$organization->id, 'public');
        }

        if ($this->event) {
            $this->authorize('update', $this->event);
            $this->event->update($payload);
            ActivityLogger::log('updated', $this->event, "Evento atualizado: {$this->event->name}");
            $this->dispatch('toast', type: 'success', title: 'Atualizado!', message: 'Evento atualizado com sucesso.');
        } else {
            $this->authorize('create', $organization);
            $payload['organization_id'] = $organization->id;
            $payload['user_id'] = Auth::id();
            $event = Event::create($payload);
            ActivityLogger::log('created', $event, "Evento criado: {$event->name}");
            $this->dispatch('toast', type: 'success', title: 'Criado!', message: 'Evento criado com sucesso.');
            $this->redirect(route('dashboard.events.edit', $event), navigate: true);

            return;
        }
    }

    public function render()
    {
        return view('livewire.events.event-form');
    }
}
