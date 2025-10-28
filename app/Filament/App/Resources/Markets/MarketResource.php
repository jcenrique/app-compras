<?php

namespace App\Filament\App\Resources\Markets;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\App\Resources\Markets\Pages\ListMarkets;
use App\Filament\App\Resources\Markets\Schemas\MarketSchema;
use App\Filament\App\Resources\Markets\Tables\MarketsTable;
use App\Models\Market;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class MarketResource extends Resource
{
    protected static ?string $model = Market::class;


    protected static string | \BackedEnum | null $navigationIcon = 'fas-building-un';
    protected static ?int $navigationSort = 10;

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



    public static function form(Schema $schema): Schema
    {
        return MarketSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
       return MarketsTable::configure($table);
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
            'index' => ListMarkets::route('/'),
            //'create' => Pages\CreateMarket::route('/create'),
            // 'edit' => Pages\EditMarket::route('/{record}/edit'),
        ];
    }
}
