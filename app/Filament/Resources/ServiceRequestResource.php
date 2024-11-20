<?php

namespace App\Filament\Resources;

use App\Enums\ServiceRequestStatusEnum;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ServiceRequest;
use Filament\Resources\Resource;
use App\Enums\ServiceRequestSectorEnum;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ServiceRequestPriorityEnum;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Support\Enums\FontWeight;
use App\Filament\Resources\ServiceRequestResource\Pages;
use App\Filament\Resources\ServiceRequestResource\RelationManagers;
use App\Models\User;

class ServiceRequestResource extends Resource
{
    protected static ?string $label = 'Chamado';
    protected static ?string $modelLabel = 'Chamado';
    protected static ?string $pluralModelLabel = 'Chamados';
    protected static ?string $navigationGroup = 'Suporte';
    protected static ?string $model = ServiceRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informações do Chamado')
                    ->description('Preencha as informações principais do chamado')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Título')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Digite o título do chamado')
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('description')
                                    ->label('Descrição')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'link',
                                        'bulletList',
                                        'orderedList',
                                    ])
                                    ->placeholder('Descreva detalhadamente o seu chamado')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Classificação')
                    ->description('Defina a categorização do chamado')
                    ->icon('heroicon-o-tag')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('sector')
                                    ->label('Setor')
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->options(ServiceRequestSectorEnum::class),

                                Forms\Components\Select::make('priority')
                                    ->options(ServiceRequestPriorityEnum::class)
                                    ->label('Prioridade')
                                    ->default(ServiceRequestPriorityEnum::MEDIUM->value)
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('assigned_to')
                                    ->label('Atribuído a')
                                    ->required()
                                    ->relationship('assignedTo', 'name', modifyQueryUsing: function (Builder $query) {
                                        $query->where('is_active', true)->where('is_admin', true);
                                    })
                                    ->default(fn() => User::where('is_active', true)->where('is_admin', true)->first()?->getKey())
                                    ->searchable()
                                    ->preload()
                                    ->native(false),
                            ]),
                    ]),

                Section::make('Anexos')
                    ->description('Adicione arquivos relevantes ao chamado')
                    ->icon('heroicon-o-paper-clip')
                    ->collapsible()
                    ->schema([
                        Forms\Components\FileUpload::make('attachments')
                            ->label('Anexos')
                            ->multiple()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/*', 'application/pdf', 'application/msword'])
                            ->disk('local')
                            ->directory('service-requests')
                            ->visibility('public')
                            ->downloadable()
                            ->openable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->limit(30)
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('sector')
                    ->label('Setor')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridade')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Responsável')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status')
                    ->placeholder('Todos os status')
                    ->trueLabel('Abertos')
                    ->falseLabel('Fechados')
                    ->queries(
                        true: fn(Builder $query) => $query->whereIn('status', [
                            ServiceRequestStatusEnum::OPEN->value,
                            ServiceRequestStatusEnum::IN_PROGRESS->value,
                            ServiceRequestStatusEnum::SCHEDULED->value,
                        ]),
                        false: fn(Builder $query) => $query->whereIn('status', [
                            ServiceRequestStatusEnum::CLOSED->value,
                            ServiceRequestStatusEnum::REJECTED->value,
                        ]),
                    ),

                Tables\Filters\SelectFilter::make('sector')
                    ->label('Setores')
                    ->multiple()
                    ->preload()
                    ->options(ServiceRequestSectorEnum::class),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioridade')
                    ->multiple()
                    ->preload()
                    ->options(ServiceRequestPriorityEnum::class),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('like')
                        ->label('Curtir')
                        ->color('success')
                        ->icon('heroicon-o-hand-thumb-up')
                        ->badge(fn(ServiceRequest $record) => $record->likes()->like()->count())
                        ->action(function (ServiceRequest $record) {
                            $record->likes()->unlike()->byUser(auth()->user())->delete();
                            if ($record->likes()->like()->byUser(auth()->user())->exists()) {
                                $record->likes()->like()->byUser(auth()->user())->delete();
                            } else {
                                $record->likes()->create(['type' => 'like', 'user_id' => auth()->id()]);
                            }
                        }),

                    Tables\Actions\Action::make('unlike')
                        ->label('Não Curtir')
                        ->color('danger')
                        ->icon('heroicon-o-hand-thumb-down')
                        ->badge(fn(ServiceRequest $record) => $record->likes()->unlike()->count())
                        ->action(function (ServiceRequest $record) {
                            $record->likes()->like()->byUser(auth()->user())->delete();
                            if ($record->likes()->unlike()->byUser(auth()->user())->exists()) {
                                $record->likes()->unlike()->byUser(auth()->user())->delete();
                            } else {
                                $record->likes()->create(['type' => 'unlike', 'user_id' => auth()->id()]);
                            }
                        }),

                    Tables\Actions\ViewAction::make()
                        ->label('Visualizar'),

                    Tables\Actions\EditAction::make()
                        ->label('Editar')
                        ->visible(fn($record) => $record->created_by === auth()->id() || auth()->user()?->is_admin ?? false),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()?->is_admin ?? false),
                ]),
            ])
            ->defaultSort('updated_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('60s');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\LogsRelationManager::class,
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', [
            ServiceRequestStatusEnum::OPEN->value,
            ServiceRequestStatusEnum::IN_PROGRESS->value,
            ServiceRequestStatusEnum::SCHEDULED->value,
        ])->count();
    }
}