<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'event_id',
        'order_id',
        'ticket_type_id',
        'ticket_batch_id',
        'participant_name',
        'participant_email',
        'participant_document',
        'participant_phone',
        'participant_company',
        'participant_role',
        'price_paid',
        'status',
        'qr_code',
        'checked_in_at',
        'checked_in_by',
        'check_in_ip',
        'custom_fields',
        'notes',
    ];

    protected $casts = [
        'price_paid' => 'decimal:2',
        'checked_in_at' => 'datetime',
        'custom_fields' => 'array',
        'status' => TicketStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->code)) {
                $ticket->code = 'TKT-'.strtoupper(Str::random(12));
            }
            if (empty($ticket->qr_code)) {
                $ticket->qr_code = Str::uuid()->toString();
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function ticketBatch(): BelongsTo
    {
        return $this->belongsTo(TicketBatch::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    public function isCheckedIn(): bool
    {
        return $this->checked_in_at !== null;
    }

    public function qrData(): string
    {
        return json_encode([
            'code' => $this->code,
            'event_id' => $this->event_id,
            'ticket_id' => $this->id,
            'type' => 'eventflow-ticket',
        ]);
    }
}
