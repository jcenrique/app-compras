<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;

use Filament\Resources\Pages\Page;
use App\Enum\OrderStatus;

use App\Models\Category;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Tables\Columns\ProductImageColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;

use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;


use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action as ActionsAction;

use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ShopOrder extends  Page implements HasTable
{
    protected static string $resource = OrderResource::class;

    protected static string $view = 'filament.app.resources.order-resource.pages.shop-order';
    use InteractsWithRecord;
    use InteractsWithTable;

    protected static bool $isLazy = false;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
    public  function getTitle(): string
    {

        return __('common.order_resource_label', ['order' => self::getRecord()->id]);
    }

    protected function getFormSchema(): array
    {
        return [
            // Fieldset::make(__('common.order_resource_label'))
            //     ->schema([
            //         Placeholder::make('market.name')
            //             ->hiddenLabel()
            //             ->extraAttributes(['style' => 'font-size:1.00em;padding:0;font-weight:bold;'])
            //             ->content($this->record->market->name . ' (' . $this->record->order_date?->format('d/m/Y') . ')'),

            //         Placeholder::make('Image')
            //             ->hiddenLabel()
            //             ->content(function (): HtmlString {
            //                 return new HtmlString("<img width='100' height='100' src= '" . asset(  'storage/'. $this->record->market->logo) . "')>");
            //             }),

            //         Placeholder::make('status')
            //             ->hiddenLabel()
            //             ->content($this->record->status->getLabel())
            //             ->extraAttributes(['class' => 'text-center bg-yellow-100 text-yellow-800 text-xl font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-yellow-900 dark:text-yellow-300' ])
            //     ])
            //     ->columns(3),

        ];
    }


    public    function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->query(OrderItem::where('order_id', self::getRecord()->id))
            ->defaultGroup(
                Group::make('is_basket')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)

                    ->getTitleFromRecordUsing(function (OrderItem $record) {
                        return $record->is_basket ? __('common.is_basket') : __('common.order_statuses.pending');
                    })
            )

            ->columns([
                ToggleColumn::make('is_basket')
                    ->label(__('common.basket'))
                    ->extraAttributes(['class' => 'w-[20]'])

                    ->afterStateUpdated(function ($record, $state) {


                        //si ya estan todos los articulos en la cesta preguntar si se desea finalizar el pedido
                        if ($state) {

                            $allItemsInBasket = $record->order->items()->where('is_basket', true)->count();
                            if ($allItemsInBasket === $record->order->items()->count()) {

                                $record->order->update(['status' => OrderStatus::COMPLETED]);


                                // Preguntar con un dialogo modal si se desea finalizar el pedido
                                Notification::make()
                                    ->title(__('common.all_items_in_basket'))
                                    ->icon('heroicon-o-check-circle')
                                    ->iconColor('success')
                                    ->body(__('common.finalize_order_confirmation'))
                                    ->persistent()
                                    ->send();
                                redirect(ShopOrder::getUrl(['record' => $record->order->id]));
                            }
                        }
                    }),



                TextColumn::make('quantity')
                    ->label(__('common.quantity'))
                    ->tooltip(__('common.change_quantity_tooltip'))
                    ->action(
                        ActionsAction::make('change_quantity')

                            ->icon('heroicon-o-pencil')
                            ->label(__('common.change_quantity'))
                            ->form(fn($record) => [
                                TextInput::make('quantity')
                                    ->label(__('common.quantity'))
                                    ->default($record->quantity)
                                    ->numeric()
                                    ->required(),

                            ])
                            ->modalWidth(MaxWidth::Medium)
                            ->action(fn($record, array $data) => $record->update(['quantity' => $data['quantity']]))
                        //->visible(fn() => auth()->user()->can('edit_name'))
                    )
                    ->badge(),

                 ProductImageColumn::make('producto')
                    ->label(__('common.product'))
                    ->tooltip(fn($record) => $record->product->name),
                    
                // ImageColumn::make('product.image')
                //     ->label(__('common.image'))
                //     ->circular()

                //     ->size(50),
                // TextColumn::make('product.name')
                //     ->label(__('common.product'))
                //      ->formatStateUsing(function ($record): HtmlString {
                //         // Return only the first <div> from the product description
                //         if (preg_match('/<div[^>]*>.*?<\/div>/is', $record->product->description, $matches)) {
                //             return new HtmlString($matches[0]);
                //         }
                //         return $record->product->name;

                //     })
                //     ->sortable()
                //     ->searchable(),

                // TextColumn::make('price')
                //     ->label(__('common.price'))
                //     ->money('EUR')
                //     ->sortable(),


                TextColumn::make('total')

                    ->label(__('common.subtotal'))
                    ->state(function (OrderItem $record): float {
                        return $record->price * $record->quantity;
                    })
                    ->summarize(
                        Summarizer::make()
                            ->prefix(new HtmlString('<strong class="danger">' .  __('common.total') . ': </strong>'))

                            ->using(function ($query) {
                                $items = $query->get();
                                $total = 0;
                                foreach ($items as $item) {
                                    $total += $item->price * $item->quantity;
                                }
                                return $total;
                            })
                            ->money('EUR')
                    )
                    ->money('EUR'),



            ])
            ->filters([
                //
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }
    protected function getHeaderActions(): array
    {
        return [

            Action::make(__('common.finalize_order'))
                ->color('success')
                ->icon('heroicon-m-check')

                ->hiddenLabel(true)
                ->tooltip(function () {
                    return __('common.finalize_order');
                })
                ->requiresConfirmation()
                ->modalIcon('heroicon-o-check')
                ->modalSubmitActionLabel(__('Yes') . ', ' . Str::lower(__('common.finalize_order')))
                ->action(function () {

                    $this->record->update(['status' => OrderStatus::COMPLETED]);
                })
                ->hidden(fn() => $this->record->status == OrderStatus::COMPLETED)

                ->visible(fn() => $this->record->status != OrderStatus::COMPLETED),

            Action::make(__('common.open_order'))
                ->color('warning')
                ->icon('fas-arrow-rotate-left')
                ->hiddenLabel(true)
                ->visible(fn() => $this->record->status == OrderStatus::COMPLETED)
                ->tooltip(__('common.open_order'))
                ->disabled(fn() => $this->record->items->where('is_basket', false)->isEmpty())
                ->requiresConfirmation()
                ->modalSubmitActionLabel(__('Yes') . ', ' . Str::lower(__('common.open_order')))
                ->action(function () {
                    if ($this->record->status = OrderStatus::COMPLETED) {


                        $this->record->update(['status' => OrderStatus::PENDING]);
                    }
                }),


            Action::make(__('common.save_pending'))
                ->form([
                  DatePicker::make('new_order_date')
                                        ->label(__('common.order_date'))
                                        ->displayFormat('d/m/Y')
                                        ->default(now())
                                        ->required(),
                ])
                ->color('warning')
                ->disabled(fn() => $this->record->items->where('is_basket', false)->isEmpty())
                ->icon('fas-save')
                //->label(__('common.save_pending'))
                ->hiddenLabel(true)
                ->tooltip(__('common.save_pending_tooltip'))
                ->requiresConfirmation()
                ->modalDescription(__('common.save_pending_tooltip'))
                ->action(function ($data) {
                    //guardar articulos pendientes del pedido en un nuevo pedido
                    $pendingItems = $this->record->items->where('is_basket', false);
                    if ($pendingItems->isEmpty()) {
                        return;
                    }
                    $newOrder = $this->record->replicate();
                    $newOrder->status = OrderStatus::PENDING;
                    $newOrder->order_date = $data['new_order_date'];
                    $newOrder->save();
                    foreach ($pendingItems as $item) {
                        $newItem = $item->replicate();
                        $newItem->order_id = $newOrder->id;
                        $newItem->save();
                    }
                    // Eliminar los artículos pendientes del pedido original
                    $this->record->items()->where('is_basket', false)->delete();
                    // Actualizar el estado del pedido original a COMPLETED
                    $this->record->update(['status' => OrderStatus::COMPLETED]);
                    // Opcionalmente, redirigir a la página del nuevo pedido
                    // return redirect()->route('orders.shop', ['record' => $newOrder]);
                    // O simplemente mostrar un mensaje de éxito

                    Notification::make()
                        ->title(__('common.pending_items_saved'))
                        ->icon('fas-floppy-disk')
                        ->iconColor('success')
                        ->send();



                    //  $this->record->update(['status' => OrderStatus::COMPLETED]);
                }),
            Action::make(__('common.add_to_basket'))
                ->color('primary')
                ->icon('fas-cart-plus')
                ->hiddenLabel(true)
                ->tooltip(__('common.add_to_basket'))
                ->requiresConfirmation()
                ->form([
                    Select::make('product_id')
                        ->label(__('common.product'))
                        ->searchable()
                        ->createOptionForm([
                            Select::make('category_id')
                                ->label(__('common.category'))
                                ->options(Category::orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->required(),

                            TextInput::make('name')
                                ->label(__('common.name'))
                                ->required(),

                            Textarea::make('description')
                                ->label(__('common.description'))
                                ->columnSpanFull(),
                            TextInput::make('price')
                                ->label(__('common.price'))
                                ->default(0)
                                ->required()
                                ->numeric()
                                ->prefix('€'),
                            TextInput::make('brand')
                                ->label(__('common.brand'))
                                ->maxLength(255),
                            FileUpload::make('image')
                                ->label(__('common.image'))
                                ->directory('images/products')
                                ->imageEditor()
                                ->image()
                                ->columnSpanFull(),
                        ])
                        ->createOptionUsing(function (array $data, Set $set) {
                            $data['market_id'] = $this->getRecord()->market_id; // Asegurarse de que el mercado se establece correctamente
                            $data['active'] = true; // Asegurarse de que el producto se crea como activo
                            // Verificar si el producto ya existe EN LOS PRODUCTOS del supermercad
                            $existingProduct = Product::where('name', trim($data['name']))->where('market_id', $data['market_id'])->first();
                            if ($existingProduct) {
                                Notification::make()
                                    ->title(__('common.product_already_exists'))
                                    ->icon('heroicon-o-exclamation-circle')
                                    ->iconColor('warning')
                                    ->send();
                                // retornar su ID si no esta en el pedido, si esta en el pedido no retornar nada
                                $existingProductInOrder = $this->getRecord()->items()->where('product_id', $existingProduct->getKey())->first();
                                if ($existingProductInOrder) {

                                    return null; // No retornar nada si ya esta en el pedido
                                }
                            }
                            // Si no existe, crear un nuevo producto
                            Notification::make()
                                ->title(__('common.product_created'))
                                ->icon('heroicon-o-check-circle')
                                ->iconColor('success')
                                ->send();


                            return Product::create($data)->getKey();
                        })
                        ->options(function () {
                            $options = [];
                            $market_id = $this->getRecord()->market_id;
                            $categories = Category::orderBy('name')->with(['products'])->get();
                            foreach ($categories as $category) {
                                if ($category->products->isEmpty()) {
                                    continue;
                                }
                                // los productos que estan en el pedido no podeben aparecer en las opciones
                                $existingProducts = $this->getRecord()->items()->pluck('product_id')->toArray();

                                $options[$category->name] = collect(Product::where('category_id', $category->id)
                                    ->where('market_id', $market_id)->active()->get())
                                    ->mapWithKeys(function ($product) use ($existingProducts) {
                                        // Filtrar productos que ya están en el pedido
                                        // si el producto ya esta en el pedido no lo muestro

                                        if (in_array($product->id, $existingProducts)) {

                                            return [];
                                        }
                                        return [$product->id => $product->name];
                                    })->toArray();
                            }





                            return $options;
                        })
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            // Establecer el precio del producto seleccionado si no esta vacio

                            $product = Product::find($get('product_id'));
                            if ($product) {
                                $set('price', $product->price);
                            }
                        })

                        ->required(),
                    TextInput::make('quantity')
                        ->label(__('common.quantity'))
                        ->default(1)
                        ->numeric()
                        ->required(),
                    TextInput::make('price')
                        ->label(__('common.price'))
                        ->readonly()
                        ->prefix('€')
                        ->numeric()
                        ->required(),

                ])
                ->action(function (array $data, Order $record) {

                    // Verificar si el producto ya está en la cesta
                    if ($this->record->items()->where('product_id', $data['product_id'])
                        ->where('is_basket', true)->exists()
                    ) {
                        Notification::make()
                            ->title(__('common.product_already_in_basket'))
                            ->icon('heroicon-o-exclamation-circle')
                            ->iconColor('warning')
                            ->send();
                        return;
                    }

                    // Añadir el producto a la cesta
                    $item = new OrderItem();
                    $item->order_id = $this->record->id;
                    $item->product_id = $data['product_id'];
                    $item->quantity = $data['quantity']; // Puedes ajustar la cantidad según sea necesario
                    $item->price = $data['price']; // Obtener el precio del producto
                    $item->is_basket = false; // Marcar como en la cesta
                    $item->save();
                    // Añadir los artículos del pedido a la cesta

                    // O simplemente mostrar un mensaje de éxito
                    Notification::make()
                        ->title(__('common.items_added_to_basket'))
                        ->icon('heroicon-o-check-circle')
                        ->iconColor('success')
                        ->send();
                }),


        ];
    }
}
