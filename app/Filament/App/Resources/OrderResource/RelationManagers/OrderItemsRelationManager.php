<?php

namespace App\Filament\App\Resources\OrderResource\RelationManagers;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';



    public function form(Form $form): Form
    {
        return $form
            // ->columns([
            //     'sm' => 1,
            //     'xl' => 2,
            //     '2xl' => 4,
            // ])

            ->schema([
                //Opcion para marcar si queremos todos los productos o solo los favoritos con un toggle
                Forms\Components\Toggle::make('show_favorites')
                    ->label(__('common.show_favorites'))
                    ->default(true)
                    ->inline(false)
                    ->columnSpan(2),

                Forms\Components\Select::make('product_id')
                    ->columnSpan(2)

                    ->label(__('common.product'))
                    ->getSearchResultsUsing(

                        function (string $search, Get $get) {
                            $market_id = $this->getOwnerRecord()->market_id;

                            // Limitar resultados y seleccionar solo los campos necesarios para mejorar el rendimiento
                            $query = Product::query()
                                ->select(['id', 'name'])
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

                            return $query->pluck('name', 'id');
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
                                return [$product->id => $product->name];
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

                Forms\Components\TextInput::make('quantity')
                    ->label(__('common.quantity'))
                    ->default(1)
                    ->maxWidth(MaxWidth::Small)
                    ->numeric()
                    ->columnSpan(2)
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label(__('common.price'))
                    ->readonly()
                    ->prefix('€')
                    ->columnSpan(2)
                    ->maxWidth(MaxWidth::Small)
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
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('common.product'))
                    // ->formatStateUsing(function ($record): HtmlString {
                    //     // Return only the first <div> from the product description
                    //     if (preg_match('/<div[^>]*>.*?<\/div>/is', $record->product->description, $matches)) {
                    //         return new HtmlString($matches[0]);
                    //     }
                    //     return $record->product->name;

                    // })
                    ->searchable(),
                Tables\Columns\ImageColumn::make('product.image')
                    ->label(__('common.image'))
                    ->circular()

                    ->size(50),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('common.quantity'))
                    ->tooltip(__('common.change_quantity_tooltip'))
                    ->action(
                        Action::make('change_quantity')

                            ->icon('heroicon-o-pencil')
                            ->label(__('common.change_quantity'))
                            ->form(fn($record) => [
                                Forms\Components\TextInput::make('quantity')
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

                Tables\Columns\TextColumn::make('price')
                    ->label(__('common.price'))
                    ->money('EUR'),

                Tables\Columns\TextColumn::make('total')

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

                Tables\Columns\IconColumn::make('is_basket')
                    ->boolean()
                    ->label(__('common.basket'))
                    ->sortable(),
            ])
            ->filters([

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modalWidth('lg')
                    //añadir el producto a favorito si no esta
                    ->action(function (array $data) {
                        $product = Product::find($data['product_id']);
                        if ($product && !$product->favorites()->where('client_id', Auth::id())->exists()) {
                            $product->favorites()->create(['client_id' => Auth::id(), 'product_id' => $product->id]);
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
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->tooltip(__('Edit'))
                    ->hiddenLabel(true),
                Tables\Actions\DeleteAction::make()
                    ->tooltip(__('Delete'))
                    ->hiddenLabel(true),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
