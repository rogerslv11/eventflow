<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Speaker extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'bio',
        'avatar_path',
        'role',
        'company',
        'email',
        'phone',
        'website',
        'social_links',
        'is_featured',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Speaker $speaker) {
            if (empty($speaker->slug)) {
                $speaker->slug = Str::slug($speaker->name);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_speaker')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function avatarUrl(): string
    {
        if ($this->avatar_path) {
            return asset('storage/'.$this->avatar_path);
        }

        return "https://api.dicebear.com/7.x/initials/svg?seed={$this->name}&backgroundColor=7C3AED&textColor=ffffff";
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
