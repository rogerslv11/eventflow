<?php

namespace App\Models;

use App\Enums\VendorCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'company_name',
        'email',
        'phone',
        'document',
        'category',
        'cost',
        'address',
        'notes',
        'contacts',
        'status',
    ];

    protected $casts = [
        'category' => VendorCategory::class,
        'cost' => 'decimal:2',
        'contacts' => 'array',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
