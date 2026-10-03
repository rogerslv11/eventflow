<?php

namespace App\Models;

use App\Enums\ScheduleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'speaker_id',
        'title',
        'description',
        'type',
        'room',
        'starts_at',
        'ends_at',
        'capacity',
        'is_featured',
    ];

    protected $casts = [
        'type' => ScheduleType::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }

    public function duration(): int
    {
        if (! $this->starts_at || ! $this->ends_at) {
            return 0;
        }

        return $this->ends_at->diffInMinutes($this->starts_at);
    }
}
