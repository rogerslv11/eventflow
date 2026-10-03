<?php

namespace App\Services;

use App\Enums\CheckInStatus;
use App\Enums\TicketStatus;
use App\Models\CheckIn;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class CheckInService
{
    /**
     * @return array{status: string, ticket: ?Ticket, message: string}
     */
    public function checkInByCode(string $codeOrUuid, ?int $eventId = null): array
    {
        $ticketQuery = Ticket::query()
            ->with(['event', 'ticketType']);

        // Try by code first
        $ticket = $ticketQuery->where('code', $codeOrUuid)
            ->orWhere('qr_code', $codeOrUuid)
            ->first();

        if (! $ticket) {
            $this->log(null, null, CheckInStatus::Invalid, $codeOrUuid);

            return [
                'status' => CheckInStatus::Invalid->value,
                'ticket' => null,
                'message' => 'Ingresso inválido ou não encontrado.',
            ];
        }

        if ($eventId && $ticket->event_id !== $eventId) {
            $this->log($ticket->event, $ticket, CheckInStatus::Invalid, $codeOrUuid);

            return [
                'status' => CheckInStatus::Invalid->value,
                'ticket' => $ticket,
                'message' => 'Ingresso não pertence a este evento.',
            ];
        }

        if ($ticket->status === TicketStatus::Cancelled) {
            $this->log($ticket->event, $ticket, CheckInStatus::Cancelled, $codeOrUuid);

            return [
                'status' => CheckInStatus::Cancelled->value,
                'ticket' => $ticket,
                'message' => 'Ingresso cancelado.',
            ];
        }

        if ($ticket->isCheckedIn()) {
            $this->log($ticket->event, $ticket, CheckInStatus::Duplicate, $codeOrUuid);

            return [
                'status' => CheckInStatus::Duplicate->value,
                'ticket' => $ticket,
                'message' => 'Check-in já foi realizado anteriormente.',
            ];
        }

        $now = now();
        $ticket->update([
            'checked_in_at' => $now,
            'status' => TicketStatus::Used,
            'checked_in_by' => Auth::id() ? (string) Auth::id() : 'system',
            'check_in_ip' => Request::ip(),
        ]);

        $ticket->event->increment('check_ins_count');

        $this->log($ticket->event, $ticket, CheckInStatus::Success, $codeOrUuid);

        return [
            'status' => CheckInStatus::Success->value,
            'ticket' => $ticket->fresh(),
            'message' => 'Check-in confirmado com sucesso!',
        ];
    }

    protected function log($event, $ticket, CheckInStatus $status, string $code): void
    {
        CheckIn::create([
            'event_id' => $event?->id,
            'ticket_id' => $ticket?->id,
            'user_id' => Auth::id(),
            'status' => $status,
            'code_scanned' => $code,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'checked_in_at' => now(),
        ]);
    }
}
