<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'user_id',
        'category_id',
        'name',
        'slug',
        'description',
        'content',
        'image_path',
        'banner_path',
        'venue_name',
        'address',
        'city',
        'state',
        'zipcode',
        'latitude',
        'longitude',
        'starts_at',
        'ends_at',
        'timezone',
        'capacity',
        'tickets_sold',
        'check_ins_count',
        'status',
        'is_featured',
        'is_public',
        'settings',
        'metadata',
        'published_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'settings' => 'array',
        'metadata' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'status' => EventStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Event $event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->name);
            }
            // Ensure unique slug per organization
            $original = $event->slug;
            $counter = 1;
            while (static::where('organization_id', $event->organization_id)
                ->where('slug', $event->slug)
                ->exists()) {
                $event->slug = $original.'-'.$counter++;
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class)->orderBy('sort_order');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(EventStaff::class);
    }

    public function staffRoles(): HasMany
    {
        return $this->hasMany(StaffRole::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class, 'event_speaker')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('event_speaker.sort_order');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class)->orderBy('starts_at');
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(Sponsor::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(EventReview::class);
    }

    public function waitlist(): HasMany
    {
        return $this->hasMany(Waitlist::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(EventTask::class)->orderBy('sort_order');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(EventExpense::class);
    }

    public function totalExpenses(bool $onlyPaid = false): float
    {
        $query = $this->expenses();
        if ($onlyPaid) {
            $query->where('status', EventExpense::STATUS_PAID);
        } else {
            $query->where('status', '!=', EventExpense::STATUS_CANCELLED);
        }

        return (float) $query->sum('amount');
    }

    public function netProfit(): float
    {
        return $this->totalRevenue() - $this->totalExpenses(true);
    }

    public function bannerUrl(): string
    {
        if ($this->banner_path) {
            return asset('storage/'.$this->banner_path);
        }

        return 'https://placehold.co/1200x400/7C3AED/FFFFFF?text='.urlencode($this->name);
    }

    public function imageUrl(): string
    {
        if ($this->image_path) {
            return asset('storage/'.$this->image_path);
        }

        return 'https://placehold.co/600x400/4C1D95/FFFFFF?text='.urlencode($this->name);
    }

    public function isUpcoming(): bool
    {
        return $this->starts_at && $this->starts_at->isFuture();
    }

    public function isOngoing(): bool
    {
        return $this->starts_at && $this->ends_at
            && now()->between($this->starts_at, $this->ends_at);
    }

    public function isFinished(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function availableTicketsCount(): int
    {
        return $this->ticketTypes()->sum('quantity') - $this->ticketTypes()->sum('sold');
    }

    public function totalRevenue(): float
    {
        return (float) $this->orders()->where('status', 'paid')->sum('total');
    }

    public function checkInRate(): float
    {
        if ($this->tickets_sold === 0) {
            return 0;
        }

        return ($this->check_ins_count / $this->tickets_sold) * 100;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', EventStatus::Published->value)
            ->orWhere('status', EventStatus::Active->value);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
