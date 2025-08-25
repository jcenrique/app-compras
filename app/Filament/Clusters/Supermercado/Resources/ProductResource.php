<?php

namespace App\Filament\Clusters\Supermercado\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use App\Models\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Supermarket;

use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use Filament\Support\Enums\MaxWidth;

use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use App\Filament\Clusters\Supermercado;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section as ComponentsSection;
use App\Filament\Clusters\Supermercado\Resources\ProductResource\Pages;
use Filament\Tables\Columns\Layout\Stack;

//use Filament\Forms\Components\Section;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Supermercado::class;

    protected static ?int $navigationSort = 5;

    public static function getNavigationBadgeColor(): ?string
    {
        return  'success';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getModelLabel(): string
    {
        return __('Product');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Products');
    }

    public static function form(Form $form): Form
    {
        // dd($form->model);
        return $form
            ->schema([
                Checkbox::make('all')
                    ->label(__('Create the same product in all supermarkets'))
                    ->default(true)
                    ->live()
                    ->hiddenOn('edit'),


                ComponentsSection::make(__('Product'))
                    ->icon('heroicon-m-shopping-cart')
                    ->description(__('Basic product data'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),

                        Select::make('supermarket_id')
                            ->label(__('Supermarket'))
                            ->relationship('supermarket', 'name')
                            ->visible(
                                function (Get $get) {
                                    if ($get('all')) {
                                        return !$get('all');
                                    } else {
                                        return true;
                                    }
                                }
                            )
                            ->live()
                            ->preload()
                            ->required()
                            ->searchable(),
                        Select::make('section_id')
                            ->columnStart(1)
                            ->relationship('category.section', 'name')
                            ->label(__('Section'))
                            // ->dehydrated(false)
                            ->live()
                             ->preload()
                            ->options(
                                Section::orderBy('name')->pluck('name', 'id')
                            )
                            ->afterStateUpdated(fn(Set $set) => $set('category_id', null))
                            ->searchable()
                            ->required(),

                        Select::make('category_id')
                            ->label(__('Category'))
                            ->live()
                            ->options(function (?Product $record, Get $get, Set $set) {
                                if (! empty($record) && empty($get('section_id'))) {
                                    $set('section_id', $record->category->section_id);
                                    $set('category_id', $record->category_id);
                                }

                                return Category::where('section_id', $get('section_id'))->pluck('name', 'id');
                            })
                            //->placeholder(fn(Get $get): string => empty($get('section_id')) ? __('First choose the section') : __('Select an option'))
                            ->required()
                            ->searchable()
                           ,



                    ]),

                ComponentsSection::make(__('Units and Price'))
                    ->icon('heroicon-m-shopping-bag')
                    ->description(__('Additional product data'))
                    ->columns(3)
                    ->schema([
                        Select::make('unit_id')
                            ->label(__('Units'))
                            ->relationship('unit', 'name')
                            ->live()
                            ->preload()
                            ->required()
                            ->searchable(),

                        TextInput::make('units_quantity')
                            ->label(__('Quantity'))
                            ->required()
                            ->numeric()
                            ->default(1),

                        TextInput::make('price')
                            ->label(__('Price'))
                            ->required()
                            ->numeric()
                            ->prefix('€'),

                    ]),


                FileUpload::make('image')
                    ->columnSpan('full')
                    ->label(__('Image'))
                    ->directory('product-images')
                    ->imageEditor()
                    ->image(),



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),

                TextColumn::make('category.section.name')
                    ->label(__('Section'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('units_quantity')

                    ->label(__('Units'))
                    ->width('1%')
                    ->numeric()
                    ->description(fn(Product $record): string => $record->unit->name),

                TextColumn::make('price')
                    ->label(__('Price'))
                    ->money('EUR')
                    ->width('1%')
                    ->sortable(),

                ImageColumn::make('image')
                    ->label(__('Image'))

                    // ->defaultImageUrl(function(Product $record) {
                    //     return url('product-images/' . $record->image);
                    // })
                    ->defaultImageUrl(url('app_compras/storage/app/public/product-images/01JQBA3P7E3MF9R2FPESM99P00.png'))
                    ->circular(),

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
                SelectFilter::make('supermarket_id')
                    ->label(__('Supermarket'))
                    ->options(fn(): array => Supermarket::query()->pluck('name', 'id')->all())
            ])
            ->groups([
                Group::make('supermarket.name')
                    ->label(__('Supermarket'))
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),

                Group::make('category.name')
                    ->label(__('Categories'))
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            ])

            ->defaultGroup('supermarket.name')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            //  'create' => Pages\CreateProduct::route('/create'),
            // 'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generate')
                ->url('some url'),
        ];
    }
}
