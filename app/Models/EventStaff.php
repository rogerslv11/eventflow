<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventStaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'staff_role_id',
        'name',
        'email',
        'phone',
        'document',
        'type',
        'status',
        'permissions',
        'notes',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(StaffRole::class, 'staff_role_id');
    }
}
