<?php

namespace App\Enums;

enum EventStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Active = 'active';
    case Finished = 'finished';
    case Cancelled = 'cancelled';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Rascunho',
            self::Published => 'Publicado',
            self::Active => 'Em andamento',
            self::Finished => 'Finalizado',
            self::Cancelled => 'Cancelado',
            self::Archived => 'Arquivado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'neutral',
            self::Published => 'primary',
            self::Active => 'success',
            self::Finished => 'neutral',
            self::Cancelled => 'danger',
            self::Archived => 'neutral',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Draft => 'file-text',
            self::Published => 'check-circle',
            self::Active => 'play-circle',
            self::Finished => 'flag',
            self::Cancelled => 'x-circle',
            self::Archived => 'archive',
        };
    }
}
