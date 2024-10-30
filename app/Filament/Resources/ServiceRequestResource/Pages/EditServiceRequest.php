<?php

namespace App\Filament\Resources\ServiceRequestResource\Pages;

use Filament\Actions;
use App\Enums\ServiceRequestStatusEnum;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\ServiceRequestResource;
use Carbon\Carbon;

class EditServiceRequest extends EditRecord
{
    protected static string $resource = ServiceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('schedule')
                ->label('Agendar')
                ->form([
                    DateTimePicker::make('scheduled_at')
                        ->label('Agendar para')
                        ->required(),
                ])
                ->requiresConfirmation()
                ->visible(fn() => auth()->user()->is_admin && $this->record->status === ServiceRequestStatusEnum::OPEN)
                ->action(function ($data) {
                    $this->record->logs()->create([
                        'message' => 'Tarefa agendada para ' . Carbon::createFromDate($data['scheduled_at'])->format('d/m/Y H:i') . ' por ' . auth()->user()->name,
                        'action' => 'update',
                        'context' => $data,
                    ]);
                    $this->record->update([
                        'status' => ServiceRequestStatusEnum::SCHEDULED,
                        'scheduled_at' => $data['scheduled_at'],
                    ]);
                }),

            Actions\Action::make('start_processing')
                ->label('Iniciar tarefa')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn() => auth()->user()->is_admin && $this->record->status === ServiceRequestStatusEnum::OPEN)
                ->action(
                    function () {
                        $this->record->logs()->create([
                            'message' => 'Tarefa iniciada por ' . auth()->user()->name,
                            'action' => 'update',
                            'context' => [
                                'started_at' => now(),
                            ],
                        ]);
                        $this->record->update([
                            'status' => ServiceRequestStatusEnum::IN_PROGRESS,
                            'started_at' => now(),
                        ]);
                    }
                ),

            Actions\Action::make('finish_processing')
                ->label('Finalizar tarefa')
                ->requiresConfirmation()
                ->visible(fn() => auth()->user()->is_admin && $this->record->status === ServiceRequestStatusEnum::IN_PROGRESS)
                ->action(function () {
                    $this->record->update([
                        'status' => ServiceRequestStatusEnum::CLOSED,
                        'completed_at' => now(),
                    ]);

                    $this->record->logs()->create([
                        'message' => 'Tarefa finalizada por ' . auth()->user()->name,
                        'action' => 'update',
                        'context' => [
                            'completed_at' => now(),
                        ],
                    ]);
                }),

            Actions\DeleteAction::make()
                ->label('Deletar')
                ->action(function () {
                    $this->record->logs()->delete();
                    $this->record->logs()->create([
                        'message' => 'Registro deletado por ' . auth()->user()->name,
                        'action' => 'delete',
                        'context' => [],
                    ]);
                    $this->record->delete();
                })
                ->visible(fn() => auth()->user()->is_admin),
        ];
    }

    protected function authorizeAccess(): void
    {
        if (auth()->user()->is_admin) {
            return;
        }
        abort_unless($this->record->created_by === auth()->id(), 403);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->record->logs()->create([
            'message' => 'Registro atualizado por ' . auth()->user()->name,
            'action' => 'update',
            'context' => $data,
        ]);

        return $data;
    }
}
