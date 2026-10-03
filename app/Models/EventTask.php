<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'event_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'assigned_to',
        'sort_order',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public const STATUS_TODO = 'todo';

    public const STATUS_DOING = 'doing';

    public const STATUS_DONE = 'done';

    public static function statuses(): array
    {
        return [
            self::STATUS_TODO => 'A fazer',
            self::STATUS_DOING => 'Em andamento',
            self::STATUS_DONE => 'Concluída',
        ];
    }

    public static function priorities(): array
    {
        return [
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            'urgent' => 'Urgente',
        ];
    }

    public function isOverdue(): bool
    {
        return $this->status !== self::STATUS_DONE
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
