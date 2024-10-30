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
    case STOCK = 'stock';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMINISTRATIVE => 'Administrativo',
            self::SECHEDULE => 'Agendamento',
            self::CANCELAMENT => 'Cancelamento',
            self::STOCK => 'Estoque',
            self::FINANCIAL => 'Financeiro',
            self::HR => 'RH',
            self::IT => 'TI',
            self::MAINTENANCE => 'Manutenção',
            self::SECURITY => 'Segurança',
            self::EXTERNAL_TECHNICIAN => 'Técnico Externo',
            self::OTHER => 'Outro',
        };
    }
}
