<?php

namespace App\Filament\Resources\ServiceRequestResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('message')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('action')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('context')
                    ->afterStateHydrated(function ($state, Set $set) {
                        $set('context', json_encode($state));
                    })
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('message')
            ->columns([
                Tables\Columns\TextColumn::make('message')
                    ->searchable()
                    ->label('Mensagem'),

                Tables\Columns\TextColumn::make('action')
                    ->searchable()
                    ->label('Ação'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Há')
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
            ])
            ->defaultSort('created_at', 'desc');
    }
}
