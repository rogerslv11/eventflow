<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Livewire\Expenses\ExpenseTable;
use App\Livewire\Orders\OrderTable;
use App\Livewire\Public\EventReviewForm;
use App\Livewire\Public\WaitlistForm;
use App\Livewire\Reviews\ReviewTable;
use App\Livewire\Tasks\TaskBoard;
use App\Livewire\Waitlist\WaitlistTable;
use App\Models\Event;
use App\Models\EventReview;
use App\Models\EventTask;
use App\Models\Order;
use App\Models\Organization;
use App\Models\User;
use App\Models\Waitlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NewFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsMember(): array
    {
        $org = Organization::factory()->create();
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->organizations()->attach($org->id, ['role' => 'owner', 'joined_at' => now()]);
        $this->actingAs($user);
        $event = Event::factory()->create(['organization_id' => $org->id, 'user_id' => $user->id]);

        return [$user, $org, $event];
    }

    public function test_orders_page_and_refund_flow(): void
    {
        [$user, $org, $event] = $this->actingAsMember();
        $order = Order::factory()->create([
            'organization_id' => $org->id,
            'event_id' => $event->id,
            'status' => OrderStatus::Paid,
        ]);

        Livewire::test(OrderTable::class)
            ->assertSee($order->code)
            ->call('refund', $order->id);

        $this->assertEquals(OrderStatus::Refunded, $order->fresh()->status);
        $this->get(route('dashboard.orders.index'))->assertOk();
    }

    public function test_public_review_submit_and_admin_toggle(): void
    {
        [$user, $org, $event] = $this->actingAsMember();

        Livewire::test(EventReviewForm::class, ['event' => $event])
            ->set('name', 'Maria')
            ->set('rating', 5)
            ->set('comment', 'Evento incrível!')
            ->call('submit')
            ->assertSee('Obrigado');

        $this->assertDatabaseHas('event_reviews', ['event_id' => $event->id, 'name' => 'Maria']);
        $this->assertEquals(5.0, EventReview::averageForEvent($event->id));

        $review = EventReview::first();
        Livewire::actingAs($user)->test(ReviewTable::class)
            ->assertSee('Maria')
            ->call('toggleApproval', $review->id);

        $this->assertFalse($review->fresh()->is_approved);
        $this->get(route('dashboard.reviews.index'))->assertOk();
    }

    public function test_waitlist_join_and_notify(): void
    {
        [$user, $org, $event] = $this->actingAsMember();

        Livewire::test(WaitlistForm::class, ['event' => $event])
            ->set('name', 'João')
            ->set('email', 'joao@example.com')
            ->set('quantity', 2)
            ->call('submit')
            ->assertSee('lista');

        $this->assertDatabaseHas('waitlists', ['event_id' => $event->id, 'email' => 'joao@example.com']);

        $entry = Waitlist::first();
        Livewire::actingAs($user)->test(WaitlistTable::class)
            ->assertSee('joao@example.com')
            ->call('notify', $entry->id)
            ->call('convert', $entry->id);

        $this->assertEquals(Waitlist::STATUS_CONVERTED, $entry->fresh()->status);
        $this->get(route('dashboard.waitlist.index'))->assertOk();
    }

    public function test_tasks_create_and_advance(): void
    {
        [$user, $org, $event] = $this->actingAsMember();

        Livewire::actingAs($user)->test(TaskBoard::class)
            ->set('filterEvent', (string) $event->id)
            ->set('title', 'Reservar som')
            ->set('event_id', (string) $event->id)
            ->set('priority', 'high')
            ->call('save')
            ->assertSee('Reservar som');

        $task = EventTask::first();
        $this->assertNotNull($task);

        Livewire::actingAs($user)->test(TaskBoard::class)
            ->call('advance', $task->id);

        $this->assertEquals(EventTask::STATUS_DOING, $task->fresh()->status);
        $this->get(route('dashboard.tasks.index'))->assertOk();
    }

    public function test_expenses_create_and_profit_stats(): void
    {
        [$user, $org, $event] = $this->actingAsMember();

        Order::factory()->paid()->create([
            'organization_id' => $org->id,
            'event_id' => $event->id,
            'total' => 1000,
        ]);

        Livewire::actingAs($user)->test(ExpenseTable::class)
            ->set('filterEvent', (string) $event->id)
            ->set('title', 'Aluguel do espaço')
            ->set('amount', '400')
            ->set('event_id', (string) $event->id)
            ->set('category', 'local')
            ->set('status', 'paid')
            ->call('save')
            ->assertSee('Aluguel do espaço');

        $this->assertDatabaseHas('event_expenses', ['event_id' => $event->id, 'title' => 'Aluguel do espaço']);
        $this->assertEquals(400.0, $event->fresh()->totalExpenses(true));
        $this->assertEquals(600.0, $event->fresh()->netProfit());
        $this->get(route('dashboard.expenses.index'))->assertOk();
    }
}
