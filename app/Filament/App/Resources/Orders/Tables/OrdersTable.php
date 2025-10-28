<?php

namespace App\Filament\App\Resources\Orders\Tables;

use App\Enum\OrderStatus;
use App\Exports\OrderItemsExport;
use App\Filament\App\Resources\Orders\OrderResource;
use App\Models\Order;
use Asmit\ResizedColumn\HasResizableColumn;
use Carbon\Carbon;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class OrdersTable
{
    use HasResizableColumn;
    public static function configure(Table $table): Table
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

                TextColumn::make('order_date')
                    ->icon('heroicon-o-calendar')
                    ->color('info')
                    ->label(__('common.order_date'))
                    ->badge()
                    ->dateTime('d/m/Y')
                    ->sortable(),

                //   Stack::make([
                TextColumn::make('notes')
                    ->label(__('common.notes'))
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->extraAttributes(['class' => 'max-w-md']),
                TextColumn::make('market.name')
                    ->label(__('common.market'))
                    ->badge()

                    ->searchable()
                    ->sortable(),







                TextColumn::make('items_count')
                    ->badge()
                    ->color('info')
                    ->label(__('common.items_count'))
                    ->icon('fas-cart-arrow-down')
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

            ]) //->from('md')

            //  ])

            ->filters([
                SelectFilter::make('status')
                    ->label(__('common.order_status'))
                    ->multiple()
                    ->options(OrderStatus::class),
            ])

            ->recordActions([

                ActionGroup::make([
                    Action::make('comprar')
                        ->hiddenLabel(true)
                        ->tooltip(__('common.comprar'))
                        ->icon(Heroicon::Eye)
                        ->url(fn(Order $record): string => OrderResource::getUrl('order-items', ['record' => $record]))
                        // ->openUrlInNewTab()
                        ->color('warning'),

                    // Action::make('comprar')
                    //     ->hiddenLabel(true)
                    //     ->tooltip(__('common.comprar'))
                    //     ->icon('heroicon-o-eye')
                    //     ->url(fn(Order $record): string => OrderResource::getUrl('shop', ['record' => $record]))
                    //     // ->openUrlInNewTab()
                    //     ->color('warning'),
                    EditAction::make()
                        ->hiddenLabel(true)
                        ->tooltip(__('Edit')),
                    DeleteAction::make()
                        ->hiddenLabel(true)
                        ->action(function (Order $record, Component $livewire) {
                            // Before deleting the order, you might want to handle related items
                            // For example, you can delete the items or set their order_id to null
                            $record->items()->delete(); // This will delete all related items
                            $record->delete(); // Now delete the order

                            $livewire->dispatch('refresh-sidebar');
                        })
                        ->tooltip(__('Delete')),

                    Action::make('copy')
                        ->hiddenLabel(true)
                        ->color('success')
                        ->label(__('common.copy_order'))
                        ->tooltip(__('common.copy'))
                        ->icon('heroicon-o-document-duplicate')
                        ->schema(fn(Order $record) => [
                            TextEntry::make('copy')
                                ->label(__('common.copy_order'))
                                ->state(fn(Order $record): string => Carbon::parse($record->order_date)->translatedFormat('d F Y')),

                            Section::make(__('common.new_order'))

                                ->description(__('common.copy_order_description'))
                                ->schema([

                                    DatePicker::make('new_order_date')
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
                                'client_id' => Auth::id(),
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
                        ->icon('heroicon-o-document-duplicate'),

                    Action::make('exportExcel')
                        ->label(__('common.export_excel'))
                        ->color('fuchsia')
                        ->icon('fas-file-excel')

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
            ->headerActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
