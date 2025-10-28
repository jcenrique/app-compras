<?php

namespace App\Filament\App\Resources\Orders\Pages;

use App\Enum\OrderStatus;
use App\Filament\App\Resources\Orders\OrderResource;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Tables\Columns\ProductImageColumn;
use Asmit\ResizedColumn\HasResizableColumn;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Component;

class ManageOrderItems extends ManageRelatedRecords
{
    // use HasResizableColumn;
    protected static string $resource = OrderResource::class;

    protected static string $relationship = 'items';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public function getBreadcrumb(): string
    {
        return self::getRecord()->client->name;
    }

    public function getTitle(): string
    {

        return __('common.order_resource_label').': '.Carbon::createFromDate(self::getRecord()->order_date)->format('d/M/Y');
    }

    public function getTabs(): array
    {

        return [
            'pending' => Tab::make()->label(__('common.order_statuses.pending'))->icon('fas-hourglass')
                ->modifyQueryUsing(fn ($query) => $query
                    ->where('is_basket', false)
                    ->join('products as p', 'order_items.product_id', '=', 'p.id')
                    ->join('categories as c', 'p.category_id', '=', 'c.id')
                    ->orderBy('c.name')
                    ->orderBy('p.name')
                    ->select('order_items.*'))
                ->badge(fn () => OrderItem::where('is_basket', false)
                    ->where('order_id', $this->getOwnerRecord()->id)
                    ->count())
                ->badgeColor('danger'),
            'in_basket' => Tab::make()->label(__('common.is_basket'))->icon('fas-shopping-basket')
                ->modifyQueryUsing(fn ($query) => $query
                    ->where('is_basket', true)
                    ->join('products as p', 'order_items.product_id', '=', 'p.id')
                    ->join('categories as c', 'p.category_id', '=', 'c.id')
                    ->orderBy('c.name')
                    ->orderBy('p.name')
                    ->select('order_items.*'))
                ->badge(fn () => OrderItem::where('is_basket', true)
                    ->where('order_id', $this->getOwnerRecord()->id)
                    ->count())
                ->badgeColor('success'),

        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product.name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
           // ->reorderableColumns()
            ->deferColumnManager(false)
            ->paginated(false)
            ->query(OrderItem::where('order_id', self::getRecord()->id))
            // crear el grupo defecto en funcion de la categoria del producto
            ->defaultGroup(
                Group::make('product.category_id')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)


                    ->getTitleFromRecordUsing(function (OrderItem $record) {
                        return  $record->product->category->name ?? __('common.uncategorized');
                    })
            )

            ->columns([
                ToggleColumn::make('is_basket')
                    ->label(__('common.basket'))
                    ->extraAttributes(['class' => 'w-[20]'])

                    ->afterStateUpdated(function ($record, $state, $livewire) {

                        // si ya estan todos los articulos en la cesta preguntar si se desea finalizar el pedido
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
                                redirect(ManageOrderItems::getUrl(['record' => $record->order->id]));
                            }
                            $this->getTabs();
                            //   $livewire->dispatch('refresh-tabs');

                        }
                    }),

                TextColumn::make('quantity')
                    ->label(__('common.quantity'))

                    ->extraAttributes(['class' => 'max-w-[80px]'])
                    ->tooltip(function ($record) {
                        if (! $record->is_basket) {
                            return __('common.change_quantity_tooltip');
                        }

                        return null;
                    })
                    ->disabledClick(fn ($record) => $record->is_basket)

                    ->action(
                        Action::make('change_quantity')

                            ->icon('heroicon-o-pencil')
                            ->label(__('common.change_quantity'))
                            ->schema(fn ($record) => [
                                TextInput::make('quantity')
                                    ->label(__('common.quantity'))
                                    ->default($record->quantity)
                                    ->numeric()
                                    ->required(),

                            ])
                            ->modalWidth(Width::Medium)
                            ->action(fn ($record, array $data) => $record->update(['quantity' => $data['quantity']]))
                        // ->visible(fn() => auth()->user()->can('edit_name'))
                    )
                    ->badge(),

                ProductImageColumn::make('producto')
                    ->label(__('common.product'))

                    ->action(
                        Action::make('verProducto')
    ->label('Ver producto')
    ->icon('heroicon-o-eye')
    ->modalHeading('Detalles del producto')
    ->modalIcon('heroicon-o-tag')
    ->modalWidth('xl')
     ->schema(function ($record) {
        return [
            ImageEntry::make('product.image')
                ->hiddenLabel()
                 ->alignCenter()
                 ->imageSize(300),

            TextEntry::make('product.name')
               ->hiddenLabel()
               ->alignCenter()
                ->size(\Filament\Support\Enums\TextSize::Large)
                ->weight(\Filament\Support\Enums\FontWeight::Bold)
                ->icon('heroicon-o-tag')
                ->iconColor('primary'),
                 TextEntry::make('product.format')
               ->hiddenLabel()
               ->hidden(fn (OrderItem $record): bool => $record->product->format === null)
               ->alignCenter()
                ->size(\Filament\Support\Enums\TextSize::Large)

                ->icon('fas-icons')
                ->iconColor('warning'),
            TextEntry::make('product.price')
               ->hiddenLabel()
               ->alignCenter()
               ->money('EUR')
                ->size(\Filament\Support\Enums\TextSize::Large)
                 ->weight(\Filament\Support\Enums\FontWeight::Black)
                ->icon('fas-coins')
                ->iconColor('danger'),
        ];
    })
    ->modalSubmitAction(false) // No botón de "Guardar"
    ->modalCancelActionLabel('Cerrar')
    ->closeModalByClickingAway(true)
    ->closeModalByEscaping(true)
    ->disabledForm()
                    )

                    ->tooltip(static fn ($record) => $record->product->name),

                TextColumn::make('total')

                    ->label(__('common.subtotal'))
                    ->state(function (OrderItem $record): float {
                        return $record->price * $record->quantity;
                    })
                    ->summarize(
                        Summarizer::make()

                            ->prefix(new HtmlString('<strong class="text-red-800">'.__('common.total').': </strong>'))

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
            ->headerActions([

                // CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([

                DeleteAction::make()->hiddenLabel(true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    // DeleteBulkAction::make(),
                ]),
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
                ->modalSubmitActionLabel(__('Yes').', '.Str::lower(__('common.finalize_order')))
                ->action(function () {

                    $this->record->update(['status' => OrderStatus::COMPLETED]);
                })
                ->hidden(fn () => $this->record->status == OrderStatus::COMPLETED)

                ->visible(fn () => $this->record->status != OrderStatus::COMPLETED),

            Action::make(__('common.open_order'))
                ->color('warning')
                ->icon('fas-arrow-rotate-left')
                ->hiddenLabel(true)
                ->visible(fn () => $this->record->status == OrderStatus::COMPLETED)
                ->tooltip(__('common.open_order'))
                ->disabled(fn () => $this->record->items->where('is_basket', false)->isEmpty())
                ->requiresConfirmation()
                ->modalSubmitActionLabel(__('Yes').', '.Str::lower(__('common.open_order')))
                ->action(function () {
                    if ($this->record->status = OrderStatus::COMPLETED) {

                        $this->record->update(['status' => OrderStatus::PENDING]);
                    }
                }),

            Action::make(__('common.save_pending'))
                ->schema([
                    DatePicker::make('new_order_date')
                        ->label(__('common.order_date'))
                        ->displayFormat('d/m/Y')
                        ->default(now())
                        ->required(),
                ])
                ->color('warning')
                ->disabled(fn () => $this->record->items->where('is_basket', false)->isEmpty())
                ->icon('fas-save')
                // ->label(__('common.save_pending'))
                ->hiddenLabel(true)
                ->tooltip(__('common.save_pending_tooltip'))
                ->requiresConfirmation()
                ->modalDescription(__('common.save_pending_tooltip'))
                ->action(function ($data, Component $livewire) {
                    // guardar articulos pendientes del pedido en un nuevo pedido
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

                    $livewire->dispatch('refresh-sidebar');
                    //  $this->record->update(['status' => OrderStatus::COMPLETED]);
                }),
            Action::make(__('common.add_to_basket'))
                ->color('primary')
                ->icon('fas-cart-plus')
                ->hiddenLabel(true)
                ->tooltip(__('common.add_to_basket'))
                ->requiresConfirmation()
                ->schema([
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
                    $item = new OrderItem;
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
