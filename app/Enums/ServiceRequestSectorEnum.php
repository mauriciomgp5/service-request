<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ServiceRequestSectorEnum: string implements HasLabel
{
    case EXTERNAL_TECHNICIAN = 'external_technician';
    case IT = 'ti';
    case MAINTENANCE = 'maintenance';
    case HR = 'hr';
    case FINANCIAL = 'financial';
    case ADMINISTRATIVE = 'administrative';
    case SECHEDULE = 'schedule';
    case SECURITY = 'security';
    case CANCELAMENT = 'cancelament';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::EXTERNAL_TECHNICIAN => 'Técnico Externo',
            self::IT => 'TI',
            self::MAINTENANCE => 'Manutenção',
            self::HR => 'RH',
            self::FINANCIAL => 'Financeiro',
            self::ADMINISTRATIVE => 'Administrativo',
            self::SECHEDULE => 'Agendamento',
            self::SECURITY => 'Segurança',
            self::CANCELAMENT => 'Cancelamento',
            self::OTHER => 'Outro',
        };
    }
}
