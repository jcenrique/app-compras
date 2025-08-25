<?php

namespace App\Filament\Clusters\Supermercado\Resources;

use App\Filament\Clusters\Supermercado;
use App\Filament\Clusters\Supermercado\Resources\SupermarketResource\Pages;
use App\Filament\Clusters\Supermercado\Resources\SupermarketResource\RelationManagers;
use App\Filament\Clusters\Supermercado\Resources\SupermarketResource\RelationManagers\ProductsRelationManager;
use App\Models\Supermarket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupermarketResource extends Resource
{
    protected static ?string $model = Supermarket::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $cluster = Supermercado::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return  'success';
    }

    public static function getModelLabel(): string
    {
        return __('Supermarket');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Supermarkets');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),

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
                Tables\Actions\EditAction::make(),
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
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupermarkets::route('/'),
            //  'create' => Pages\CreateSupermarket::route('/create'),
            'edit' => Pages\EditSupermarket::route('/{record}/edit'),
        ];
    }
}
