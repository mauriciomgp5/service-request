<?php

namespace App\Filament\Resources\ServiceRequestResource\Pages;

use App\Enums\ServiceRequestStatusEnum;
use App\Filament\Resources\ServiceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceRequest extends CreateRecord
{
    protected static string $resource = ServiceRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['status'] = ServiceRequestStatusEnum::OPEN;
        return $data;
    }
}
