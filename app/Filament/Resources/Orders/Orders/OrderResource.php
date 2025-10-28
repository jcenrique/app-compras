<?php

namespace App\Filament\Resources\Orders\Orders;


use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

use App\Enum\OrderStatus;
use App\Exports\OrderItemsExport;
use App\Filament\App\Resources\Orders\Pages\ManageOrderItems;
use App\Filament\App\Resources\Orders\Pages\ShopOrder;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\Orders\Orders\Pages\ManageOrderItemsAdmin;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\RelationManagers\ItemsRelationManager;
use App\Models\Client;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Forms;

use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

use Maatwebsite\Excel\Facades\Excel;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static bool $canCreateAnother = false;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadgeColor(): ?string
    {
        return  'danger';
    }

    public static function getNavigationBadge(): ?string
    {

        return static::getModel()::all()->count();
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
                Select::make('client_id')
                    ->label(__('common.client_resource_label'))
                    ->options(function () {
                        return Client::all()->pluck('name', 'id');
                    }),

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
        return $table

            ->striped()
            ->recordUrl(null)
            ->defaultSort('order_date', 'desc')
            ->contentGrid([
                'md' => 1,

            ])
            ->recordClasses(fn(Order $record) => match ($record->status) {
                OrderStatus::PENDING => 'border-s-2 border-yellow-600 dark:border-yellow-300 p-2',
                OrderStatus::CANCELED => 'border-s-2 border-red-600 dark:border-red-300 p-2',
                OrderStatus::COMPLETED => 'border-s-2 border-green-600 dark:border-green-300 p-2',
                default => null,
            })
            ->columns([
                // Split::make([
                TextColumn::make('status')
                    ->sortable()
                    ->action(
                        Action::make('change_status')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->label(__('common.change_status'))
                            ->schema(fn(Order $record) => [
                                ToggleButtons::make('status')
                                    ->label(__('common.order_status'))
                                    ->options(OrderStatus::class)
                                    ->default($record->status)
                                    ->inline()
                                //>grouped()

                            ])
                            ->modalWidth(Width::Medium)
                            ->action(fn(Order $record, array $data) => $record->update(['status' => $data['status']]))

                    )
                    ->label(__('common.order_status'))
                    ->badge(),
                //   Stack::make([

                TextColumn::make('client.name')
                    ->label(__('common.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label(__('common.notes'))
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->extraAttributes(['class' => 'max-w-md']),
                TextColumn::make('market.name')
                    ->label(__('common.market'))
                    ->badge()
                    ->color('success')

                    ->searchable()
                    ->sortable(),




                TextColumn::make('order_date')
                    ->icon('heroicon-o-calendar')
                    ->label(__('common.order_date'))
                    ->badge()
                    ->color('info')
                    ->dateTime('d/m/Y')
                    ->sortable(),


                TextColumn::make('items_count')
                    ->badge()
                    ->label(__('common.items_count'))
                    ->icon('fas-cart-arrow-down')
                    ->color('info')
                    // ->prefix(__('common.items_count_prefix'))
                    ->counts('items'),




                TextColumn::make('total_price')
                    ->label(__('common.total_price'))
                    ->icon('fas-coins')
                    ->color('info')
                    ->money('EUR')
                    ->badge()
                    ->getStateUsing(
                        function ($record): string {

                            $total = 0;
                            foreach ($record->items as $item) {
                                if ($item->price != null && $item->quantity != null) {
                                    $total += $item->price * $item->quantity;
                                }
                            }

                            return $total;
                        }
                    )
                    ->money('EUR'),
            ])
            ->filters([
                SelectFilter::make('client')
                    ->relationship('client', 'name')
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('comprar')
                        ->hiddenLabel(true)
                        ->tooltip(__('common.comprar'))
                        ->icon('heroicon-o-eye')
                        ->url(fn(Order $record): string => OrderResource::getUrl('order-items', ['record' => $record]))
                        // ->openUrlInNewTab()
                        ->color('warning'),
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('exportExcel')
                        ->label(__('common.export_excel'))
                        ->icon('fas-file-excel')
                        ->color('info')
                        ->action(function (Order $record) {
                            $fecha = Carbon::createFromDate($record->order_date)->format('Ymd');
                            $client = $record->client()->first()->name;

                            return Excel::download(new OrderItemsExport($record), $client . $fecha . '.xlsx');
                        }),


                ])->toolTip(__('Actions'))

                    ->color('danger')
                    ->hiddenLabel()
                    ->label(__('Actions')),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            // 'create' => Pages\CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
          //  'shop' => ShopOrder::route('/{record}/shop'),
            'order-items' => ManageOrderItemsAdmin::route('/{record}/items'),
        ];
    }
}
