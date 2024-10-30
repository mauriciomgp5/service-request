<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Filament\Resources\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $label = 'Usuário';

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('avatar_path')
                    ->label('Avatar')
                    ->disk('public')
                    ->directory('avatars')
                    ->image()
                    ->avatar(),

                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefone')
                    ->tel()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\Toggle::make('is_admin')
                    ->label('Administrador')
                    ->required(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo')
                    ->required(),

                Forms\Components\TextInput::make('password')
                    ->label('Senha')
                    ->visible(fn($record) => is_null($record))
                    ->password()
                    ->required()
                    ->confirmed()
                    ->maxLength(255),

                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Confirmação de senha')
                    ->visibleOn(['create'])
                    ->password()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_path')
                    ->label('Avatar'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->label('Administrador')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Aprovado em')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Aprovar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->is_active && empty ($record->approved_at))
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canCreate(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }
}
