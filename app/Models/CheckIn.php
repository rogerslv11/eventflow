<?php

namespace App\Models;

use App\Enums\CheckInStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_id',
        'participant_id',
        'user_id',
        'status',
        'code_scanned',
        'gate',
        'location',
        'ip_address',
        'user_agent',
        'notes',
        'checked_in_at',
    ];

    protected $casts = [
        'status' => CheckInStatus::class,
        'checked_in_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
