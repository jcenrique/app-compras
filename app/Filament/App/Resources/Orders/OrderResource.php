<?php

namespace App\Filament\App\Resources\Orders;


use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Components\Section;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\App\Resources\Orders\RelationManagers\OrderItemsRelationManager;


use Carbon\Carbon;

use App\Models\Order;
use App\Enum\OrderStatus;

use Filament\Tables\Table;

use Filament\Resources\Resource;
use App\Exports\OrderItemsExport;
use App\Filament\App\Resources\Orders\Pages\EditOrder;
use App\Filament\App\Resources\Orders\Pages\ListOrders;
use App\Filament\App\Resources\Orders\Pages\ManageOrderItems;
use App\Filament\App\Resources\Orders\Pages\ShopOrder;
use App\Filament\App\Resources\Orders\RelationManagers\ItemsRelationManager;
use App\Filament\App\Resources\Orders\Resources\OrderItems\OrderItemResource;
use App\Filament\App\Resources\Orders\Tables\OrdersTable;
use Illuminate\Support\Facades\Auth;

use Filament\Notifications\Notification;

use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\Width;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $relationship = 'items';
    protected static ?string $relatedResource = OrderItemResource::class;

    protected static bool $canCreateAnother = false;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadgeColor(): ?string
    {
        return  'success';
    }

    public static function getNavigationBadge(): ?string
    {

        return static::getModel()::where('client_id',  Auth::user()?->id)->count();
    }

    public static function getModelLabel(): string
    {
        return __('common.order_resource_label');
    }
    public static function getPluralModelLabel(): string
    {
        return __('common.order_resource_plural_label');
    }


    public static function form(Schema $schema): Schema
    {
        return $schema

            ->components([

                Select::make('market_id')
                    ->label(__('common.market'))
                    ->relationship('market', 'name')
                    ->required(),
                DatePicker::make('order_date')
                    ->label(__('common.order_date'))

                    ->displayFormat('d/m/Y')
                    ->required(),
                ToggleButtons::make('status')
                    ->label(__('common.order_status'))
                    ->options(OrderStatus::class)
                    ->default(OrderStatus::PENDING)
                    ->inline()
                    //->grouped()
                    ->required(),



                Textarea::make('notes')
                    ->label(__('common.notes'))
                    ->maxLength(500)
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [


            OrderItemsRelationManager::class,

           //ItemsRelationManager::class

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            //  'create' => Pages\CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
           // 'shop' => ShopOrder::route('/{record}/shop'),
           'order-items' => ManageOrderItems::route('/{record}/items'),
        ];
    }



    //modificar la query para cargar la relacion de productos en orden
     public static function getEloquentQuery(): Builder
    {


        return parent::getEloquentQuery()->where('client_id', Auth::user()->id)->with(['items.product.category']);


    }
}
