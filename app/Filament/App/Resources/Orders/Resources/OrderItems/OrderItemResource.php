<?php

namespace App\Filament\App\Resources\Orders\Resources\OrderItems;

use App\Filament\App\Resources\Orders\OrderResource;
use App\Filament\App\Resources\Orders\Resources\OrderItems\Pages\CreateOrderItem;
use App\Filament\App\Resources\Orders\Resources\OrderItems\Pages\EditOrderItem;
use App\Filament\App\Resources\Orders\Resources\OrderItems\Pages\ListOrderItems;
use App\Filament\App\Resources\Orders\Resources\OrderItems\Schemas\OrderItemForm;
use App\Filament\App\Resources\Orders\Resources\OrderItems\Tables\OrderItemsTable;
use App\Models\OrderItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = OrderResource::class;

    protected static ?string $recordTitleAttribute = 'product.name';

    public static function form(Schema $schema): Schema
    {
        return OrderItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {

        return OrderItemsTable::configure($table);
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

            'create' => CreateOrderItem::route('/create'),
            'edit' => EditOrderItem::route('/{record}/edit'),
        ];
    }
}
