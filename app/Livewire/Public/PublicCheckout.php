<?php

namespace App\Livewire\Public;

use App\Models\Event;
use App\Services\CheckoutService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout')]
#[Layout('layouts.public')]
class PublicCheckout extends Component
{
    public Event $event;

    public array $items = [];

    public array $customer = [];

    public ?string $couponCode = null;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function processCheckout(array $selectedItems, array $customer, ?string $couponCode = null): void
    {
        if (empty($selectedItems)) {
            $this->dispatch('toast', type: 'error', title: 'Erro', message: 'Selecione pelo menos um ingresso.');

            return;
        }

        if (empty($customer['name']) || empty($customer['email'])) {
            $this->dispatch('toast', type: 'error', title: 'Erro', message: 'Preencha nome e e-mail.');

            return;
        }

        $items = [];
        foreach ($selectedItems as $ticketTypeId => $item) {
            $items[] = [
                'ticket_type_id' => (int) $ticketTypeId,
                'quantity' => (int) $item['quantity'],
            ];
        }

        $service = app(CheckoutService::class);

        try {
            $order = $service->createOrder(
                event: $this->event,
                items: $items,
                customer: [
                    'name' => $customer['name'],
                    'email' => $customer['email'],
                    'document' => $customer['document'] ?? null,
                    'phone' => $customer['phone'] ?? null,
                    'company' => $customer['company'] ?? null,
                    'role' => $customer['role'] ?? null,
                ],
                couponCode: $couponCode,
            );

            $order = $service->processPayment($order, 'credit_card');

            $this->dispatch('toast', type: 'success', title: 'Compra confirmada!', message: 'Seu ingresso foi gerado.');

            return redirect()->route('public.checkout.success', $order->code);
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', title: 'Erro', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.public.public-checkout');
    }
}
