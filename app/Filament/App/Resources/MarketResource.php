<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\MarketResource\Pages;
use App\Filament\App\Resources\MarketResource\RelationManagers;
use App\Models\Market;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MarketResource extends Resource
{
    protected static ?string $model = Market::class;


    protected static ?string $navigationIcon = 'fas-building-un';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('common.market_management_nav_group');
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return  'success';
    }

    public static function getNavigationBadge(): ?string
    {

        return static::getModel()::count();
    }

    public static function getModelLabel(): string
    {
        return __('common.market_resource_label');
    }
    public static function getPluralModelLabel(): string
    {
        return __('common.market_resource_plural_label');
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('common.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('active')
                    ->label(__('common.active'))
                    ->default(true)
                    ->inLine(false)
                    ->required(),

                Forms\Components\FileUpload::make('logo')
                    ->label(__('common.logo'))
                    ->directory('images/logos')
                    ->imageEditor()
                    ->image()
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name','asc')
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('common.name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('logo')
                    ->label(__('common.logo'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->label(__('common.active'))
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->tooltip(__('Edit'))
                    ->hiddenLabel(true),
                Tables\Actions\DeleteAction::make()
                    ->tooltip(__('Delete'))
                    ->hiddenLabel(true),
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
            'index' => Pages\ListMarkets::route('/'),
            //'create' => Pages\CreateMarket::route('/create'),
            // 'edit' => Pages\EditMarket::route('/{record}/edit'),
        ];
    }
}
