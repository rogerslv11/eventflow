<?php

namespace App\Models;

use App\Enums\TicketType as TicketTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TicketType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'description',
        'type',
        'price',
        'quantity',
        'sold',
        'reserved',
        'min_per_order',
        'max_per_order',
        'sales_start_at',
        'sales_end_at',
        'is_active',
        'sort_order',
        'benefits',
        'settings',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sales_start_at' => 'datetime',
        'sales_end_at' => 'datetime',
        'is_active' => 'boolean',
        'benefits' => 'array',
        'settings' => 'array',
        'type' => TicketTypeEnum::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (TicketType $tt) {
            if (empty($tt->slug)) {
                $tt->slug = Str::slug($tt->name);
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(TicketBatch::class)->orderBy('sort_order');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function available(): int
    {
        return max(0, $this->quantity - $this->sold - $this->reserved);
    }

    public function isOnSale(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->sales_start_at && $this->sales_start_at->isFuture()) {
            return false;
        }

        if ($this->sales_end_at && $this->sales_end_at->isPast()) {
            return false;
        }

        return $this->available() > 0;
    }

    public function getCurrentBatch()
    {
        $now = now();

        return $this->batches()
            ->where('is_active', true)
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('sort_order')
            ->first();
    }

    public function formattedPrice(): string
    {
        if ($this->type === TicketTypeEnum::Free || $this->price == 0) {
            return 'Gratuito';
        }

        return 'R$ '.number_format((float) $this->price, 2, ',', '.');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereRaw('quantity > sold + reserved');
    }
}
