<?php

namespace App\Filament\Clusters\Supermercado\Resources\SupermarketResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use App\Models\Section;
use Filament\Forms\Set;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Hidden::make('supermaket_id'),

                Select::make('section_id')
                    ->relationship('category.section', 'name')
                    ->label(__('Section'))
                    ->dehydrated(false)
                    ->live()
                    ->options(
                        Section::orderBy('name')->pluck('name', 'id')
                    )

                    ->afterStateUpdated(fn(Set $set) => $set('category_id', null))

                    ->searchable()
                    ->required(),


               Select::make('category_id')
                    ->label(__('Category'))
                    ->live()


                    ->placeholder(fn(Forms\Get $get): string => empty($get('country_id')) ? 'First select country' : 'Select an option')

                    ->options(function (?Product $record, Forms\Get $get, Forms\Set $set) {
                        if (! empty($record) && empty($get('section_id'))) {
                            $set('section_id', $record->category->section_id);
                            $set('category_id', $record->category_id);
                        }

                        return Category::where('section_id', $get('section_id'))->pluck('name', 'id');
                    })

                    ->required()
                    ->searchable()
                    ->preload(),






                Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->live()
                    ->preload()
                    ->required()
                    ->searchable(),


                TextInput::make('units_quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('€'),
                FileUpload::make('image')
                    ->directory('product-images')
                    ->imageEditor()
                    ->image(),



            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.section.name')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('units_quantity')
                    ->label(__('Units'))
                    ->numeric()
                    ->description(fn(Product $record): string => $record->unit->name)
                    ->sortable(),


                Tables\Columns\TextColumn::make('price')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
