<?php

namespace App\Jobs;

use App\Models\Event;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEventNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $eventId,
        public string $type,
        public string $title,
        public string $message,
        public string $icon = 'bell',
        public string $color = 'primary',
        public ?string $actionUrl = null,
    ) {}

    public function handle(): void
    {
        $event = Event::find($this->eventId);
        if (! $event) {
            return;
        }

        NotificationService::notifyAdmins(
            type: $this->type,
            title: $this->title,
            message: $this->message,
            organizationId: $event->organization_id,
            icon: $this->icon,
            color: $this->color,
            actionUrl: $this->actionUrl,
        );
    }
}
