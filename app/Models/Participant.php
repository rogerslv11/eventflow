<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_id',
        'order_id',
        'user_id',
        'name',
        'email',
        'document',
        'phone',
        'company',
        'role',
        'city',
        'state',
        'status',
        'has_checked_in',
        'checked_in_at',
        'custom_fields',
        'notes',
    ];

    protected $casts = [
        'has_checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
        'custom_fields' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
