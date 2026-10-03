<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'event_id',
        'name',
        'email',
        'rating',
        'title',
        'comment',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public static function averageForEvent(int $eventId): float
    {
        return (float) static::where('event_id', $eventId)
            ->where('is_approved', true)
            ->avg('rating');
    }

    public static function npsForEvent(int $eventId): int
    {
        $total = static::where('event_id', $eventId)->where('is_approved', true)->count();
        if ($total === 0) {
            return 0;
        }
        $promoters = static::where('event_id', $eventId)->where('is_approved', true)->where('rating', 5)->count()
            + static::where('event_id', $eventId)->where('is_approved', true)->where('rating', 4)->count() * 0;
        // NPS simplificado 1-5: promotores (5), neutros (4), detratores (1-3)
        $promotersCount = static::where('event_id', $eventId)->where('is_approved', true)->where('rating', 5)->count();
        $detractorsCount = static::where('event_id', $eventId)->where('is_approved', true)->where('rating', '<=', 3)->count();

        return (int) round((($promotersCount - $detractorsCount) / $total) * 100);
    }
}
