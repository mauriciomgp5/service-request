<?php

namespace App\Filament\Resources;

use App\Enums\ServiceRequestPriorityEnum;
use App\Enums\ServiceRequestSectorEnum;
use App\Filament\Resources\ServiceRequestResource\Pages;
use App\Filament\Resources\ServiceRequestResource\RelationManagers;
use App\Models\ServiceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceRequestResource extends Resource
{
    protected static ?string $label = 'Chamado';

    protected static ?string $model = ServiceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titulo')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Descrição')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('sector')
                    ->label('Setor')
                    ->required()
                    ->options(ServiceRequestSectorEnum::class),

                Forms\Components\Select::make('priority')
                    ->options(ServiceRequestPriorityEnum::class)
                    ->label('Prioridade')
                    ->default(ServiceRequestPriorityEnum::MEDIUM->value)
                    ->required(),

                Forms\Components\Select::make('assigned_to')
                    ->label('Atribuído a')
                    ->required()
                    ->relationship('createdBy', 'name', modifyQueryUsing: function (Builder $query) {
                        $query->where('is_active', true)->where('is_admin', true);
                    }),

                Forms\Components\FileUpload::make('attachments')
                    ->label('Anexos')
                    ->disk('local')
                    ->directory('service-requests'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),

                Tables\Columns\TextColumn::make('sector')
                    ->label('Setor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Atribuído a')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('like')
                    ->label(false)
                    ->color('success')
                    ->badge(fn (ServiceRequest $serviceRequest) => $serviceRequest->likes()->like()->count())
                    ->badgeColor('success')
                    ->action(function (ServiceRequest $serviceRequest) {
                        $serviceRequest->likes()->unlike()->byUser(auth()->user())->delete();
                        if ($serviceRequest->likes()->like()->byUser(auth()->user())->exists()) {
                            $serviceRequest->likes()->like()->byUser(auth()->user())->delete();
                        } else {
                            $serviceRequest->likes()->create(['type' => 'like', 'user_id' => auth()->id()]);
                        }
                    })
                    ->icon('heroicon-o-hand-thumb-up'),

                Tables\Actions\Action::make('unlike')
                    ->label(false)
                    ->color('danger')
                    ->badge(fn (ServiceRequest $serviceRequest) => $serviceRequest->likes()->unlike()->count())
                    ->badgeColor('danger')
                    ->action(function (ServiceRequest $serviceRequest) {
                        $serviceRequest->likes()->like()->byUser(auth()->user())->delete();
                        if ($serviceRequest->likes()->unlike()->byUser(auth()->user())->exists()) {
                            $serviceRequest->likes()->unlike()->byUser(auth()->user())->delete();
                        } else {
                            $serviceRequest->likes()->create(['type' => 'unlike', 'user_id' => auth()->id()]);
                        }
                    })
                    ->icon('heroicon-o-hand-thumb-down'),

                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->created_by === auth()->id()),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceRequests::route('/'),
            'create' => Pages\CreateServiceRequest::route('/create'),
            'edit' => Pages\EditServiceRequest::route('/{record}/edit'),
        ];
    }
}
