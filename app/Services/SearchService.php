<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Speaker;
use App\Models\Vendor;

class SearchService
{
    public function globalSearch(string $query, ?int $organizationId = null, int $limit = 5): array
    {
        if (mb_strlen($query) < 2) {
            return [];
        }

        $results = [];

        // Events
        $events = Event::query()
            ->when($organizationId, fn ($q) => $q->where('organization_id', $organizationId))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('city', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'status', 'starts_at']);

        if ($events->count() > 0) {
            $results[] = [
                'label' => 'Eventos',
                'items' => $events->map(fn ($e) => [
                    'id' => $e->id,
                    'title' => $e->name,
                    'subtitle' => $e->city.' • '.$e->starts_at?->format('d/m/Y'),
                    'url' => route('dashboard.events.show', $e->id),
                    'icon' => 'calendar',
                ])->toArray(),
            ];
        }

        // Participants
        $participants = Participant::query()
            ->when($organizationId, fn ($q) => $q->whereHas('event', fn ($q2) => $q2->where('organization_id', $organizationId)))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('document', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get(['id', 'name', 'email', 'event_id']);

        if ($participants->count() > 0) {
            $results[] = [
                'label' => 'Participantes',
                'items' => $participants->map(fn ($p) => [
                    'id' => $p->id,
                    'title' => $p->name,
                    'subtitle' => $p->email,
                    'url' => route('dashboard.participants.index', ['event_id' => $p->event_id]),
                    'icon' => 'users',
                ])->toArray(),
            ];
        }

        // Orders / Tickets
        $orders = Order::query()
            ->when($organizationId, fn ($q) => $q->where('organization_id', $organizationId))
            ->where(function ($q) use ($query) {
                $q->where('code', 'like', "%{$query}%")
                    ->orWhere('customer_name', 'like', "%{$query}%")
                    ->orWhere('customer_email', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get(['id', 'code', 'customer_name', 'event_id', 'total', 'status']);

        if ($orders->count() > 0) {
            $results[] = [
                'label' => 'Pedidos',
                'items' => $orders->map(fn ($o) => [
                    'id' => $o->id,
                    'title' => $o->code.' • '.$o->customer_name,
                    'subtitle' => 'R$ '.number_format($o->total, 2, ',', '.'),
                    'url' => route('dashboard.events.show', $o->event_id),
                    'icon' => 'shopping-cart',
                ])->toArray(),
            ];
        }

        // Speakers
        $speakers = Speaker::query()
            ->when($organizationId, fn ($q) => $q->where('organization_id', $organizationId))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('company', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get(['id', 'name', 'company']);

        if ($speakers->count() > 0) {
            $results[] = [
                'label' => 'Palestrantes',
                'items' => $speakers->map(fn ($s) => [
                    'id' => $s->id,
                    'title' => $s->name,
                    'subtitle' => $s->company,
                    'url' => route('dashboard.speakers.index'),
                    'icon' => 'mic',
                ])->toArray(),
            ];
        }

        // Vendors
        $vendors = Vendor::query()
            ->when($organizationId, fn ($q) => $q->where('organization_id', $organizationId))
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('company_name', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get(['id', 'name', 'company_name']);

        if ($vendors->count() > 0) {
            $results[] = [
                'label' => 'Fornecedores',
                'items' => $vendors->map(fn ($v) => [
                    'id' => $v->id,
                    'title' => $v->name,
                    'subtitle' => $v->company_name,
                    'url' => route('dashboard.vendors.index'),
                    'icon' => 'truck',
                ])->toArray(),
            ];
        }

        return $results;
    }
}
