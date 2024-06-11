<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ServiceRequestStatusEnum: string implements HasColor, HasLabel
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

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::OPEN => 'info',
            self::REJECTED => 'danger',
            self::IN_PROGRESS => 'gray',
            self::CLOSED => 'green',
        };
    }
}
