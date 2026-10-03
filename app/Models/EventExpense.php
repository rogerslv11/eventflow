<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'event_id',
        'vendor_id',
        'title',
        'description',
        'category',
        'amount',
        'status',
        'due_date',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_CANCELLED = 'cancelled';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_PAID => 'Paga',
            self::STATUS_CANCELLED => 'Cancelada',
        ];
    }

    public static function categories(): array
    {
        return [
            'geral' => 'Geral',
            'local' => 'Local/Espaço',
            'alimentacao' => 'Alimentação',
            'som_luz' => 'Som & Iluminação',
            'marketing' => 'Marketing',
            'equipe' => 'Equipe',
            'decoracao' => 'Decoração',
            'transporte' => 'Transporte',
            'outros' => 'Outros',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
