<?php

namespace App\Filament\App\Resources;

use App\Enum\OrderStatus;
use App\Filament\App\Resources\OrderResource\Pages;
use App\Filament\App\Resources\OrderResource\Pages\EditOrder;
use App\Filament\App\Resources\OrderResource\Pages\ShopOrder;
use App\Filament\App\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static bool $canCreateAnother = false;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 1;

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


    public static function form(Form $form): Form
    {
        return $form

            ->schema([

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
                Split::make([
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
                    Tables\Columns\ImageColumn::make('market.logo'),
                    Tables\Columns\TextColumn::make('market.name')
                        ->label(__('common.market'))
                        ->badge()

                        ->searchable()
                        ->sortable(),




                    Tables\Columns\TextColumn::make('order_date')
                        ->icon('heroicon-o-calendar')
                        ->label(__('common.order_date'))
                        ->badge()
                        ->dateTime('d/m/Y')
                        ->sortable(),


                    Tables\Columns\TextColumn::make('items_count')
                        ->badge()
                        ->label(__('common.items_count'))
                        ->icon('fas-cart-arrow-down')
                        // ->prefix(__('common.items_count_prefix'))
                        ->counts('items'),




                    Tables\Columns\TextColumn::make('total_price')
                        ->label(__('common.total_price'))
                        ->icon('fas-coins')
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

            ])->from('md')

            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('common.order_status'))
                    ->multiple()
                    ->options(OrderStatus::class),
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
                    Tables\Actions\EditAction::make()
                        ->hiddenLabel(true)
                        ->tooltip(__('Edit')),
                    Tables\Actions\DeleteAction::make()
                        ->hiddenLabel(true)
                        ->tooltip(__('Delete')),
                    Tables\Actions\Action::make('copy')
                        ->hiddenLabel(true)
                        ->label(__('common.copy_order'))
                        ->tooltip(__('common.copy'))
                        ->icon('heroicon-o-document-duplicate')
                        ->form(fn(Order $record) => [
                            Placeholder::make('copy')
                                ->label(__('common.copy_order'))
                                ->content(fn(Order $record): string => Carbon::parse($record->order_date)->translatedFormat('d F Y')),

                            Section::make(__('common.new_order'))

                                ->description(__('common.copy_order_description'))
                                ->schema([

                                    Forms\Components\DatePicker::make('new_order_date')
                                        ->label(__('common.order_date'))
                                        ->displayFormat('d/m/Y')
                                        ->default(now())
                                        ->required(),
                                ])
                        ])
                        ->action(function (Order $record, $data, Order $newOrder) {


                            // $marketId = $data['new_market_id'] ?? $record->market_id;
                            $orderDate = $data['new_order_date'] ?? now();
                            // Create a new order with the same items but different market and date
                            // Note: You might want to handle the case where items are not copied
                            // or you might want to copy items as well.
                            $newOrder = Order::create([
                                'market_id' => $record->market_id,
                                'order_date' => $orderDate,
                                'client_id' => Auth::user()->id,
                                'status' => OrderStatus::PENDING,
                                'notes' => $record->notes,
                            ]);
                            // Copy items from the original order to the new order
                            foreach ($record->items as $item) {
                                $newOrder->items()->create([
                                    'product_id' => $item->product_id,
                                    'quantity' => $item->quantity,
                                    'price' => $item->price,
                                    'notes' => $item->notes,
                                ]);
                            }
                            // Redirect to the edit page of the new order
                            Notification::make()
                                ->success()
                                ->title(__('common.order_copied'))
                                ->body(__('common.order_copied_successfully', ['id' => $newOrder->id]))
                                ->send();

                            return redirect()->route('filament.app.resources.orders.edit', ['record' => Order::latest()->first()]);
                        })

                        ->requiresConfirmation()
                        ->modalHeading(__('common.copy_order'))
                        ->modalSubmitActionLabel(__('common.copy_order'))
                        ->icon('heroicon-o-document-duplicate')


                ])->toolTip(__('Actions'))
                    ->button()
                    ->color('gray')
                    ->hiddenLabel()
                    ->label(__('Actions'))
                    ->icon('fas-list'),
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


            RelationManagers\OrderItemsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            //  'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'shop' => Pages\ShopOrder::route('/{record}/shop'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('client_id', Auth::user()->id);

    }
}
