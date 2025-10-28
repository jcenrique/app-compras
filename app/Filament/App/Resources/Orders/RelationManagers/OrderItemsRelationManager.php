<?php

namespace App\Filament\App\Resources\Orders\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\Exports\ExportColumn;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Width;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;


class OrderItemsRelationManager extends RelationManager
{
   use HasResizableColumn;
    protected static string $relationship = 'items';



    public function form(Schema $schema): Schema
    {
        return $schema
            // ->columns([
            //     'sm' => 1,
            //     'xl' => 2,
            //     '2xl' => 4,
            // ])

            ->components([
                //Opcion para marcar si queremos todos los productos o solo los favoritos con un toggle
                Toggle::make('show_favorites')
                    ->label(__('common.show_favorites'))
                    ->default(true)
                    ->inline(false)
                    ->columnSpan(2),

                Select::make('product_id')
                    ->columnSpan(2)

                    ->label(__('common.product'))
                    ->getSearchResultsUsing(

                        function (string $search, Get $get) {
                            $market_id = $this->getOwnerRecord()->market_id;

                            // Limitar resultados y seleccionar solo los campos necesarios para mejorar el rendimiento
                            $query = Product::query()
                                ->select(['id', 'name', 'format'])
                                ->where('market_id', $market_id)
                                ->when(
                                    $get('show_favorites'),
                                    fn($q) => $q->whereHas('favorites', fn($q) => $q->where('client_id', Auth::id()))
                                )
                                ->when(
                                    $search,
                                    fn($q) => $q->where('name', 'like', "%{$search}%")
                                )
                                ->active()
                                ->limit(20); // Limitar el número de resultados

                            return $query->get()->mapWithKeys(function ($product) {
                                return [
                                    $product->id => "{$product->name} - {$product->format}",
                                ];
                            })->toArray();
                        }
                    )
                    ->options(function (Get $get) {
                        $market_id = $this->getOwnerRecord()->market_id;
                        $product_ids = $this->getOwnerRecord()->items->pluck('product_id')->toArray();

                        // Obtener todos los productos favoritos activos del mercado de una vez si esta marcada la opcion de favoritos
                        $products = Product::where('market_id', $market_id)
                            ->when(
                                $get('show_favorites'),
                                fn($q) =>
                                $q->whereHas('favorites', fn($q) => $q->where('client_id', Auth::id()))
                            )
                            ->whereNotIn('id', $product_ids)
                            ->active()
                            ->with('category')
                            ->get();

                        // Agrupar productos por categoría
                        $grouped = $products->groupBy(fn($product) => optional($product->category)->name);

                        // Construir el array de opciones
                        $options = [];
                        foreach ($grouped as $categoryName => $productsInCategory) {
                            if (!$categoryName) {
                                continue;
                            }
                            $options[$categoryName] = $productsInCategory->mapWithKeys(function ($product) {
                                $options = $product->format ? $product->name .  $product->format  : $product->name;


                                return [$product->id => $options];
                            })->toArray();
                        }

                        return $options;
                    })

                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {


                        //si hay producto
                        if ($get('product_id')) {
                            $set('price', Product::find($get('product_id'))->price);
                        } else {
                            $set('quantity', 1);
                            $set('price', 0);
                        }
                    })
                    ->preload()
                    ->searchable()
                    ->required(),

                TextInput::make('quantity')
                    ->label(__('common.quantity'))
                    ->default(1)
                    // ->maxWidth(Width::Small)
                    ->numeric()
                    // ->columnSpan(2)
                    ->required(),
                TextInput::make('price')
                    ->label(__('common.price'))
                    ->readonly()
                    ->prefix('€')
                    //->columnSpan(2)
                    //   ->maxWidth(Width::Small)
                    ->numeric()
                    ->required(),


            ]);
    }

    public function table(Table $table): Table
    {
        return $table

            ->striped()
            ->extremePaginationLinks()
            ->defaultPaginationPageOption('all')
            ->heading(__('common.order_items_plural_label'))
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('common.product'))
                    ->description(fn($record) => $record->product->format ? __('common.format') . ': ' . $record->product->format : null)
                    ->searchable(),
                ImageColumn::make('product.image')
                    ->label(__('common.image'))
                    ->circular()

                    ->size(50),
                TextColumn::make('quantity')
                    ->label(__('common.quantity'))
                    ->tooltip(__('common.change_quantity_tooltip'))
                    ->action(
                        Action::make('change_quantity')

                            ->icon('heroicon-o-pencil')
                            ->label(__('common.change_quantity'))
                            ->schema(fn($record) => [
                                TextInput::make('quantity')
                                    ->label(__('common.quantity'))
                                    ->default($record->quantity)
                                    ->numeric()
                                    ->required(),

                            ])
                            ->modalWidth(Width::Medium)
                            ->action(fn($record, array $data) => $record->update(['quantity' => $data['quantity']]))
                        //->visible(fn() => auth()->user()->can('edit_name'))
                    )
                    ->badge(),

                TextColumn::make('price')
                    ->label(__('common.price'))
                    ->money('EUR'),

                TextColumn::make('total')

                    ->label(__('common.subtotal'))
                    ->state(function (OrderItem $record): float {
                        return $record->price * $record->quantity;
                    })
                    ->summarize(
                        Summarizer::make()
                            ->prefix(new HtmlString('<strong class="text-red-800">' .  __('common.total') . ': </strong>'))

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

                IconColumn::make('is_basket')
                    ->boolean()
                    ->label(__('common.basket'))
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([

                CreateAction::make()->modalWidth('lg')
                    //añadir el producto a favorito si no esta
                    ->action(function (array $data, array $arguments, Action $action, Schema $schema) {

                        $product = Product::find($data['product_id']);
                        if ($product && !$product->favorites()->where('client_id', Auth::id())->exists()) {
                            $product->favorites()->attach(['client_id' => Auth::id()]);
                        }
                        //guardar el producto en los productos del pedido
                        $order = $this->getOwnerRecord();
                        if ($order && !$order->items()->where('product_id', $product->id)->exists()) {
                            $order->items()->create([
                                'product_id' => $product->id,
                                'quantity' => $data['quantity'] ?? 1,
                                'price' => $product->price,
                            ]);
                        }
                        if ($arguments['another'] ?? false) {

                            $schema->fill();
                            $action->halt();
                        }
                    }),



            ])
            ->recordActions([

                DeleteAction::make()
                    ->tooltip(__('Delete'))
                    ->hiddenLabel(true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),


            ]);
    }



    public static function getModelLabel(): string
    {
        return __('common.order_items_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.order_items_label');
    }
}
