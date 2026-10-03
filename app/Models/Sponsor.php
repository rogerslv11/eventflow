<?php

namespace App\Models;

use App\Enums\SponsorTier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'organization_id',
        'name',
        'logo_path',
        'website',
        'description',
        'tier',
        'contribution',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'tier' => SponsorTier::class,
        'contribution' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function logoUrl(): string
    {
        if ($this->logo_path) {
            return asset('storage/'.$this->logo_path);
        }

        return 'https://placehold.co/300x120/27272A/A1A1AA?text='.urlencode($this->name);
    }
}
