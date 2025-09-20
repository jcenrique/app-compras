<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\OrderItem;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('product.name')
                    ->required()
                    ->maxLength(255),
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
                //
            ])
            ->headerActions([
                

            ])
            ->actions([
               
                Tables\Actions\DeleteAction::make(),
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
