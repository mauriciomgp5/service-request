<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ServiceRequestStatusEnum: string implements HasLabel, HasColor
{

    case OPEN = 'open';
    case REJECTED = 'rejected';
    case IN_PROGRESS = 'in_progress';
    case CLOSED = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::OPEN => 'Aberto',
            self::REJECTED => 'Rejeitado',
            self::IN_PROGRESS => 'Em andamento',
            self::CLOSED => 'Fechado',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::OPEN => 'blue',
            self::REJECTED => 'red',
            self::IN_PROGRESS => 'yellow',
            self::CLOSED => 'green',
        };
    }
}
