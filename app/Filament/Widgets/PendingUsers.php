<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingUsers extends BaseWidget
{

    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string|Htmlable|null
    {
        return 'Usuários Pendentes';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                UserResource::getModel()::query()
                    ->where('is_active', false)
                    ->where('approved_at', null)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Aprovar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn() => auth()->user()?->is_admin ?? false)
                    ->action(function (User $user) {
                        $user->update([
                            'is_active' => true,
                            'approved_at' => now()
                        ]);

                        Notification::make()
                            ->title('Usuário Aprovado')
                            ->body("O usuário {$user->name} foi aprovado com sucesso.")
                            ->success()
                            ->send();
                    })
                    ->icon('heroicon-o-check-circle'),
            ]);
    }
}
