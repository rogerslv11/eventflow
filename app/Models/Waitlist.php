<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Waitlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'event_id',
        'ticket_type_id',
        'name',
        'email',
        'phone',
        'quantity',
        'status',
        'notified_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'notified_at' => 'datetime',
    ];

    public const STATUS_WAITING = 'waiting';

    public const STATUS_NOTIFIED = 'notified';

    public const STATUS_CONVERTED = 'converted';

    public const STATUS_CANCELLED = 'cancelled';

    public static function statuses(): array
    {
        return [
            self::STATUS_WAITING => 'Aguardando',
            self::STATUS_NOTIFIED => 'Notificado',
            self::STATUS_CONVERTED => 'Convertido',
            self::STATUS_CANCELLED => 'Cancelado',
        ];
    }

    public function label(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }
}
