<?php

namespace App\Filament\Resources;

use App\Enum\OrderStatus;
use App\Exports\OrderItemsExport;
use App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Models\Client;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Forms;

use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

use Maatwebsite\Excel\Facades\Excel;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static bool $canCreateAnother = false;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label(__('common.client_resource_label'))
                    ->options(function () {
                        return Client::all()->pluck('name', 'id');
                    }),

                Forms\Components\Select::make('market_id')
                    ->label(__('common.market'))
                    ->relationship('market', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('order_date')
                    ->label(__('common.order_date'))

                    ->displayFormat('d/m/Y')
                    ->required(),
                Forms\Components\ToggleButtons::make('status')
                    ->label(__('common.order_status'))
                    ->options(OrderStatus::class)
                    ->default(OrderStatus::PENDING)
                    ->inline()
                    //->grouped()
                    ->required(),



                Forms\Components\Textarea::make('notes')
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
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->action(
                        Action::make('change_status')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->label(__('common.change_status'))
                            ->form(fn(Order $record) => [
                                ToggleButtons::make('status')
                                    ->label(__('common.order_status'))
                                    ->options(OrderStatus::class)
                                    ->default($record->status)
                                    ->inline()
                                //>grouped()

                            ])
                            ->modalWidth(MaxWidth::Medium)
                            ->action(fn(Order $record, array $data) => $record->update(['status' => $data['status']]))

                    )
                    ->label(__('common.order_status'))
                    ->badge(),
                //   Stack::make([

                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('common.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('market.name')
                    ->label(__('common.market'))
                    ->badge()
                    ->color('success')

                    ->searchable()
                    ->sortable(),




                Tables\Columns\TextColumn::make('order_date')
                    ->icon('heroicon-o-calendar')
                    ->label(__('common.order_date'))
                    ->badge()
                    ->color('info')
                    ->dateTime('d/m/Y')
                    ->sortable(),


                Tables\Columns\TextColumn::make('items_count')
                    ->badge()
                    ->label(__('common.items_count'))
                    ->icon('fas-cart-arrow-down')
                    ->color('info')
                    // ->prefix(__('common.items_count_prefix'))
                    ->counts('items'),




                Tables\Columns\TextColumn::make('total_price')
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
            ->actions([
                ActionGroup::make([
                    Tables\Actions\Action::make('comprar')
                        ->hiddenLabel(true)
                        ->tooltip(__('common.comprar'))
                        ->icon('heroicon-o-eye')
                        ->url(fn(Order $record): string => OrderResource::getUrl('shop', ['record' => $record]))
                        // ->openUrlInNewTab()
                        ->color('warning'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('exportExcel')
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
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

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
            'index' => Pages\ListOrders::route('/'),
            // 'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'shop' => Pages\ShopOrder::route('/{record}/shop'),
        ];
    }
}
