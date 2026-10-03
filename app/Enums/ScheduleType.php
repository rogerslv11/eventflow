<?php

namespace App\Enums;

enum ScheduleType: string
{
    case Talk = 'talk';
    case Workshop = 'workshop';
    case Panel = 'panel';
    case Break_ = 'break';
    case Show = 'show';
    case Activity = 'activity';
    case Opening = 'opening';
    case Closing = 'closing';

    public function label(): string
    {
        return match ($this) {
            self::Talk => 'Palestra',
            self::Workshop => 'Workshop',
            self::Panel => 'Painel',
            self::Break_ => 'Intervalo',
            self::Show => 'Show',
            self::Activity => 'Atividade',
            self::Opening => 'Abertura',
            self::Closing => 'Encerramento',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Talk => 'primary',
            self::Workshop => 'success',
            self::Panel => 'primary',
            self::Break_ => 'neutral',
            self::Show => 'warning',
            self::Activity => 'primary',
            self::Opening => 'success',
            self::Closing => 'danger',
        };
    }
}
