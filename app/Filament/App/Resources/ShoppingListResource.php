<?php

namespace App\Filament\App\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enum\StatusShopping;
use App\Models\ShoppingList;
use Filament\Resources\Resource;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\App\Resources\ShoppingListResource\Pages;
use App\Filament\App\Resources\ShoppingListResource\RelationManagers;
use App\Filament\App\Resources\ShoppingListResource\RelationManagers\ShoppingListProductsRelationManager;
use App\Models\Product;
use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Hidden;

use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\ToggleButtons;


use Illuminate\Support\Number;

class ShoppingListResource extends Resource
{
    protected static ?string $model = ShoppingList::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getModelLabel(): string
    {
        return __('Shopping list');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Shopping lists');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('purchase_date')
                    ->label(__('Purchase date'))
                    ->columnSpan(2)
                    ->required(),

                Select::make('supermarket_id')
                    ->columnSpan(3)
                    ->label(__('Supermarket'))
                    ->relationship('supermarket', 'name')
                    ->disabledOn('edit')
                    ->preload()
                    ->required()
                    ->searchable(),

                Hidden::make('user_id'),

                // Select::make('user_id')
                //     ->columnSpan(3)
                //     ->label(__('User'))
                //     ->relationship('user', 'name')
                //     ->default(Auth::user()->id)
                //     ->required()
                //     ->preload()
                //     ->searchable(),
                ToggleButtons::make('status')
                    ->columnSpan(3)
                    ->hiddenOn('create')
                    ->label(__('Status'))
                    ->options(StatusShopping::class)
                    ->default(StatusShopping::PENDING)
                    ->required()
                    ->inline(),

                Textarea::make('description')
                    ->label(__('Description'))
                    ->columnSpan('full')
                    ->maxLength(255),

                Section::make(__('Products'))
                    ->hiddenOn('create')
                    ->description(__('Articulos seleccionados para comprar'))
                    ->icon('heroicon-m-shopping-bag')
                    ->schema([

                        Repeater::make('listItems')
                            ->label('')
                            ->defaultItems(0)
                            ->relationship()
                            ->columnSpan(6)
                            ->addActionLabel(__('Añadir producto'))
                            ->schema([

                                Select::make('product_id')
                                    ->columnSpan(3)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('price', number_format($product->price, 2));

                                            $cantidad = $get('quantity');
                                            $precio = $get('price');
                                            $set('total', number_format($cantidad * $precio, 2));
                                        }
                                    })
                                    ->label(__('Product'))
                                    ->options(function (Get $get) {

                                        $supermarket_id = $get('../../supermarket_id'); // $livewire->getOwnerRecord()->supermarket_id;

                                        return Product::where('supermarket_id', $supermarket_id)->pluck('name', 'id');

                                        //return Product::all()->pluck('name', 'id');
                                    })
                                    ->live()
                                    ->preload()
                                    ->searchable()
                                    ->required()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                TextInput::make('quantity')
                                    ->label(__('Quantity'))
                                    ->live()
                                    ->default(1)
                                    ->dehydrated()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $cantidad = $get('quantity');
                                        $precio = $get('price');
                                        if (is_numeric($cantidad) &  is_numeric($precio)) {
                                            $set('total', number_format($cantidad * $precio, 2));
                                        }
                                    })
                                    ->afterStateHydrated(function ($state, Forms\Get $get, Forms\Set $set) {
                                        $set('total', number_format($get('price') * $state ?? 0, 2));
                                    })
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(100)
                                    ->required(),

                                TextInput::make('price')
                                    ->label(__('Price'))
                                    ->live()
                                    ->required()
                                    ->prefix('€')
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {

                                        $cantidad = $get('quantity');
                                        $precio = $get('price');
                                        if (is_numeric($cantidad) &  is_numeric($precio)) {
                                            $set('total', number_format($cantidad * $precio, 2));
                                        }
                                    })
                                    ->numeric()
                                    ->step(0.05),

                                TextInput::make('total')
                                    ->dehydrated()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {

                                        self::updateTotals($get, $set);
                                    })
                                    ->afterStateHydrated(function ($state, callable $set, callable $get) {

                                        self::updateTotals($get, $set);
                                    })
                                    ->label(__('Total'))
                                    ->readOnly()
                                    ->numeric()
                                    ->prefix('€'),

                            ])
                            ->reorderableWithButtons()
                            ->orderColumn('order')
                            ->live()
                            // After adding a new row, we need to update the totals
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::updateTotals($get, $set);
                            })
                            // After deleting a row, we need to update the totals
                            ->deleteAction(
                                fn(Action $action) => $action->after(fn(Get $get, Set $set) => self::updateTotals($get, $set)),
                            )
                            ->columns(6)
                    ]),

                TextInput::make('total_general')
                    ->columnSpan(2)
                    ->inlineLabel()
                    ->columnStart(7)
                    ->prefix('€')
                    ->dehydrated()

                    ->numeric()
                    ->hiddenOn('create')
                    ->readOnly()
                    // Live field, as we need to re-calculate the total on each change
                    ->live(true)
                    // This enables us to display the subtotal on the edit page load
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::updateTotals($get, $set);
                    })
                    ->afterStateHydrated(function (Get $get, Set $set) {
                        self::updateTotals($get, $set);
                    }),

            ])->columns(8);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('purchase_date')
                    ->label(__('Purchase date'))
                    ->date('d F Y')
                    ->sortable(),



                TextColumn::make('supermarket.name')
                    ->label(__('Supermarket'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('Description'))
                    ->searchable(),


                TextColumn::make('status')

                    ->badge()
                    // ->action(function (ShoppingList $record): void {
                    //     if ($record->status == StatusShopping::PENDING) {
                    //         $record->status = StatusShopping::FINISHED;
                    //     } else {
                    //         $record->status = StatusShopping::PENDING;
                    //     }
                    //     $record->save();
                    // })
                    ->action(
                        Tables\Actions\Action::make('select')

                            ->requiresConfirmation(function (Tables\Actions\Action $action, $record) {
                                //$action->modalDescription('Are you sure you want to set this as the default pipeline stage?');
                                $action->modalHeading(__('Change status'));

                                return $action;
                            })
                            ->action(function (ShoppingList $record): void {
                                if ($record->status == StatusShopping::PENDING) {
                                    $record->status = StatusShopping::FINISHED;
                                } else {
                                    $record->status = StatusShopping::PENDING;
                                }
                                $record->save();
                            }),
                    )


                    ->label(__('Status')),



                TextColumn::make('listItems.product_id')
                    ->label(__('Products'))
                    ->formatStateUsing(function ($state) {

                        return Product::whereIn('id', explode(',', $state))->get()->count();
                    }),
                TextColumn::make('listItems.price')
                    ->label(__('Price'))

                    ->formatStateUsing(function ($state) {
                        $sum_price = array_sum(explode(',', $state));
                        return Number::currency($sum_price, 'EUR');
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(''),
                DeleteAction::make()
                    ->label('')
                    ->requiresConfirmation(),



            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //  ShoppingListProductsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShoppingLists::route('/'),
            // 'create' => Pages\CreateShoppingList::route('/create'),
            'edit' => Pages\EditShoppingList::route('/{record}/edit'),
        ];
    }


    public static function updateTotals(Get $get, Set $set): void
    {

        // Retrieve all selected products and remove empty rows
        $selectedProducts = collect($get('listItems'))->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity'])  && !empty($item['price']));


        // Retrieve prices for all selected products
        $prices = $selectedProducts->pluck('price', 'product_id');

        //   dd($prices );


        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
            return $subtotal + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);

        // Update the state with the new values
        ///     $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('total_general', number_format($subtotal + ($subtotal * ($get('taxes') / 100)), 2, '.', ''));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            //->withCount('listItems_products')
        ;
    }
}
