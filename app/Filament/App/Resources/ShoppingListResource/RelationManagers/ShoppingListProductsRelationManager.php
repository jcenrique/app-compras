<?php

namespace App\Filament\App\Resources\ShoppingListResource\RelationManagers;

use App\Models\Product;
use App\Models\ShoppingListShoppingListProduct;
use App\Models\Supermarket;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\Model;

class ShoppingListProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'shopping_list_shopping_list_product';

   

    public static function getModelLabel(): string
    {
        return __('Shopping list product');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Shopping list products');
    }

public function getTableHeading(): string
    {
        return __('Lista de productos'); // Título personalizado
    }
    public function form(Form $form): Form
    {

        return $form
            ->schema([


                Repeater::make('shopping_list_shopping_list_product')
                   ->relationship()
                    ->columnSpan(6)
                    ->addActionLabel(__('Añadir producto'))
                    ->schema([
                        Select::make('product_id')
                            ->columnSpan(3)
                            ->label(__('Product'))
                            ->options(function (RelationManager $livewire) {
                                $supermarket_id = $this->getRelationship()->getParent()->supermarket_id; // $livewire->getOwnerRecord()->supermarket_id;

                                return Product::where('supermarket_id', $supermarket_id)->pluck('name', 'id');
                            })
                            ->afterStateUpdated(fn(Set $set) => $set('price', 10))
                            ->live()
                            ->preload()
                            ->searchable()
                            ->required()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                        TextInput::make('quantity')
                            ->label(__('Quantity'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)

                            ->required(),
                        TextInput::make('price')
                            ->label(__('Price'))
                            ->prefix('€')
                            ->numeric(),
                        TextInput::make('total')
                            ->label(__('Total'))
                            ->numeric()
                            ->prefix('€'),


                    ])->columns(6)
            ])->columns(6);
    }

    public function table(Table $table): Table
    {
        return $table
           // ->recordTitleAttribute('product.name')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label(__('Product'))->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->summarize(
                          
                        Count::make()->label('')
                            ->prefix('Total productos: '),
                    ),
                
                    Tables\Columns\TextColumn::make('price') 
                    ->label(__('Price'))
                    ->money('EUR'),
        



                Tables\Columns\TextColumn::make('total')
                 ->label(__('Total'))
                 ->money('EUR')
                    ->getStateUsing(function (Model $record) {
                        // return whatever you need to show
                        return $record->quantity * $record->price;
                    })

                    ->summarize(
                        Summarizer::make()
                        ->prefix('Total compra: ')
                            
                             ->money('EUR')
                            ->using(function (Table $table) {
                                return $table->getRecords()->sum('total');
                            })
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->createAnother(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
