<?php

namespace App\Filament\App\Pages;

use App\Enum\OrderStatus;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Darryldecode\Cart\Facades\CartFacade;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;
use Livewire\Component;

class Cart extends Page implements HasTable
{
    use HasPageShield;
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'fas-shopping-cart';

    protected string $view = 'filament.app.pages.cart';

    protected static ?int $navigationSort = 2;

    public function getTitle(): string|Htmlable
    {
        return __('common.shopping_cart');
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('common.items_in_cart');
    }

    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadge(): ?string
    {

        return CartFacade::session(Auth::id())->getContent()->count() ?: null;
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('clear_cart')
                ->label(__('common.clear_cart'))
                ->color('danger')
                ->icon('fas-trash')
                ->requiresConfirmation()
                ->action(function (Component $livewire) {
                    CartFacade::session(Auth::id())->clear();
                    Notification::make()
                        ->title(__('common.cart_cleared'))
                        ->success()
                        ->send();
                    $livewire->dispatch('refresh-sidebar');
                })
                ->visible(CartFacade::session(Auth::id())->getContent()->count() > 0),

        ];
    }

    public function table(Table $table): Table
    {
        //  dd(CartFacade::session(Auth::id())->getContent()->toArray());

        $products = Product::whereIn('id', CartFacade::session(Auth::id())->getContent()->pluck('id')->toArray())->OrdenPorCategoriaYNombre()->get()->keyBy('id');

        return $table
            ->poll('2s')
            ->query(Product::query())
            ->records(function () use ($products) {

                return $products; // CartFacade::session(Auth::id())->getContent()->toArray();
            })

            ->defaultGroup(
                Group::make('category.name')

                    ->collapsible()
                    ->titlePrefixedWithLabel(false)

                    ->getTitleFromRecordUsing(function (Product $record) {
                        return $record->category->name ?? __('common.uncategorized');
                    })
            )
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->withAggregate('category', 'name')
                    ->orderBy('category_name')
                    ->orderBy('name')->select('products.*');
            })
            ->columns([
                TextColumn::make('name'),

                TextColumn::make('name')
                    ->description(
                        function ($record) {

                            return $record->format ? $record->format : '';
                        }
                    )
                    ->label(__('common.product'))
                    ->searchable()
                    ->sortable()
                    ->wrap()

                    ->extraAttributes(['class' => 'max-w-md']),

                ImageColumn::make('image')
                    ->label(__('common.image'))

                    ->imageSize(50)
                    ->tooltip(fn ($record): string => $record->name)
                    ->extraAttributes(['class' => 'mx-auto']),

                TextColumn::make('quantity')
                    ->label(__('common.quantity'))
                    ->getStateUsing(fn ($record) => CartFacade::session(Auth::id())->get($record->id)?->quantity ?? 0),
                TextColumn::make('price')
                    ->label(__('common.price'))
                    ->money('eur', true),

                TextColumn::make('total')
                    ->label(__('common.subtotal'))
                    ->money('eur', true)
                    // ->getStateUsing(fn ($record): float => $record->price * (CartFacade::session(Auth::id())->get($record->id)?->quantity ?? 0))

                    ->summarize(
                        Summarizer::make('total')

                            ->prefix(new HtmlString('<strong class="text-red-800">'.__('common.total').': </strong>'))
                            ->using(function (Builder $query) use ($products): string {
                                // obtener los productos por la category en la agrupacion
                                $categoryproducts = $query->get();
                                // obtener los productos del carrito que esten en los productos de la categoria
                                // IDs de productos en esta categoría
                                $categoryProductIds = $categoryproducts->pluck('id');

                                // Filtrar los productos del carrito que están en esta categoría
                                $myCartProducts = $products->whereIn('id', $categoryProductIds);

                                // Realizar la operación deseada (ej. sumar precios)
                                $total = $myCartProducts->sum('price');

                                return Number::currency($total, 'EUR', 'es');

                            })

                    )

                    ->money('EUR'),

            ])
            ->filters([
                // ...
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make(__('common.delete'))

                        ->tooltip(__('common.delete'))
                        ->hiddenLabel(true)
                        ->icon('fas-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('common.delete'))
                        ->action(function (Product $record, Component $livewire) {
                            CartFacade::session(Auth::id())->remove($record->id);
                            Notification::make()
                                ->title(__('common.item_removed_from_cart'))
                                ->success()
                                ->send();
                            $livewire->dispatch('refresh-sidebar');
                        }),
                    Action::make(__('common.decrease_quantity'))

                        ->tooltip(__('common.decrease_quantity'))
                        ->hiddenLabel(true)
                        ->icon('fas-minus')
                        ->color('warning')
                        ->action(function (Product $record, Component $livewire) {
                            $item = CartFacade::session(Auth::id())->get($record->id);
                            if ($item && $item->quantity > 1) {
                                CartFacade::session(Auth::id())->update($record->id, [
                                    'quantity' => -1,
                                ]);
                                Notification::make()
                                    ->title(__('common.item_quantity_decreased'))
                                    ->success()
                                    ->send();
                            } else {
                                CartFacade::session(Auth::id())->remove($record->id);
                                Notification::make()
                                    ->title(__('common.item_removed_from_cart'))
                                    ->success()
                                    ->send();
                            }
                            $livewire->dispatch('refresh-sidebar');

                        }),
                    Action::make(__('common.increase_quantity'))

                        ->tooltip(__('common.increase_quantity'))
                        ->hiddenLabel(true)
                        ->icon('fas-plus')
                        ->color('success')
                        ->action(function (Product $record, Component $livewire) {
                            CartFacade::session(Auth::id())->update($record->id, [
                                'quantity' => 1,
                            ]);
                            Notification::make()
                                ->title(__('common.item_quantity_increased'))
                                ->success()
                                ->send();
                            $livewire->dispatch('refresh-sidebar');
                            $livewire->dispatch('table.refresh');
                        }),
                ])

                    ],RecordActionsPosition::BeforeColumns)

            ->toolbarActions([
                Action::make('order now')
                    ->label(__('common.order_now'))
                    ->color('success')
                    ->icon('fas-bag-shopping')
                    ->requiresConfirmation()
                    ->action(function (Component $livewire) {
                        // crear orden nueva con los productos del carrito
                        $cartItems = CartFacade::session(Auth::id())->getContent();
                        $client = Client::find(Auth::id());

                        $order = $client->orders()->create([
                            'market_id' => Number::parseInt($cartItems->first()->attributes['associatedModel']->market_id),
                            'order_date' => now(),

                            'client_id' => Auth::id(),
                            'status' => OrderStatus::PENDING,
                        ]);
                        foreach ($cartItems as $item) {
                            $product = Product::find($item->id);
                            if ($product) {
                                $order->items()->create([
                                    'product_id' => $product->id,
                                    'quantity' => $item->quantity,
                                    'price' => $item->price,
                                ]);
                            }
                        }
                        Notification::make()
                            ->title(__('common.order_placed_successfully'))
                            ->success()
                            ->send();
                        CartFacade::session(Auth::id())->clear();
                        $livewire->dispatch('refresh-sidebar');
                        $livewire->redirect(route('filament.app.resources.orders.edit', $order));
                    })
                    ->visible(CartFacade::session(Auth::id())->getContent()->count() > 0),
            ]);
    }
}
