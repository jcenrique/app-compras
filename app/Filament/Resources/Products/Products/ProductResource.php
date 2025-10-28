<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Component as Livewire;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 24;

    public static function getNavigationGroup(): ?string
    {
        return __('common.market_management_nav_group');
    }

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
        return __('common.product_resource_label');
    }
    public static function getPluralModelLabel(): string
    {
        return __('common.product_resource_plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('common.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('active')
                    ->label(__('common.active'))
                    ->default(true)
                    ->inline(false)
                    ->required(),

                Forms\Components\RichEditor::make('description')

                    ->label(__('common.description'))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->label(__('common.price'))
                    ->default(0)
                    ->required()
                    ->numeric()
                    ->prefix('€'),
                Forms\Components\TextInput::make('brand')
                    ->label(__('common.brand'))
                    ->maxLength(255),

                Forms\Components\Select::make('section_id')
                    ->live()
                    ->label(__('common.section_resource_label'))
                    ->dehydrated(false)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('category_id', null);
                    })
                    ->relationship(
                        name: 'category.section',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->active()->orderBy('name'),
                    )

                    ->searchable()
                    ->required()
                    ->preload(),
                Forms\Components\Select::make('category_id')
                    ->label(__('common.category'))
                    ->options(function (?Product $record, Forms\Get $get, Forms\Set $set) {
                        if (! empty($record) && empty($get('section_id'))) {
                            $set('section_id', $record->category->section_id);
                            $set('category_id', $record->category_id);
                        }
                        return Category::where('section_id', $get('section_id'))->active()->orderBy('name')->pluck('name', 'id');
                    })
                    ->live()
                    ->searchable()
                    ->required()
                    ->preload(),

                Forms\Components\Select::make('market_id')
                    ->label(__('common.market'))
                    ->relationship(
                        name: 'market',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->active()->orderBy('name'),
                    )
                    ->disabled(function (Get $get) {
                        return $get('is_unique_market') !== true;
                    })
                    ->live()
                    ->searchable()
                    ->required(function (Get $get) {
                        return $get('is_unique_market') == true;
                    })

                    ->preload(),

                Forms\Components\Toggle::make('is_unique_market')
                    ->hiddenOn('edit')
                    ->label(__('common.is_unique_market'))
                    ->default(false)
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if (!$state) {
                            $set('market_id', null);
                        }
                    })

                    ->inline(false)
                    ->required(),

                Forms\Components\FileUpload::make('image')
                    ->label(__('common.image'))
                    ->directory('images/products')
                    ->imageEditor()
                    ->image()
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('category.name')
            ->defaultPaginationPageOption(25)
            ->defaultSort('name', 'asc')
            ->groups([
                Group::make('category.name')
                    ->titlePrefixedWithLabel(false)
                    ->getDescriptionFromRecordUsing(function (Product $record) {
                        if ($record->category->description) {
                            return $record->category->description;
                        }

                        //return '';

                    })
                    ->label(__('common.category'))
                    ->collapsible(),
                Group::make('market.name')
                    ->titlePrefixedWithLabel(false)
                    ->label(__('common.market'))
                    ->collapsible(),


            ])


            ->columns([
                Grid::make()
                    ->grow(true)
                    ->columns(1)
                    ->schema([
                        Split::make([
                            Grid::make()
                                ->columns(1)
                                ->schema([
                                    Tables\Columns\ImageColumn::make('image')
                                        ->alignment(Alignment::Center)
                                        ->size(150),
                                ])->grow(false),


                            Grid::make()
                                ->columns(1)
                                ->schema([

                                    Tables\Columns\TextColumn::make('category.name')
                                        ->color('gray')
                                        ->prefix(__('common.category') . ': ')
                                        ->hidden(function (Table $table) {
                                            if ($table->getGrouping() && $table->getGrouping()->getId() === 'category.name') {
                                                return true;
                                            }
                                        })
                                        ->label(__('common.category'))
                                        ->sortable(),
                                    //columna para marcar favoritos
                                    Split::make([
                                        Tables\Columns\TextColumn::make('text1')
                                            ->color('warning')
                                            ->default(function ($record) {
                                                return __('common.is_favorite');
                                            }),


                                    ]),


                                    Grid::make()
                                        ->columns(1)
                                        ->schema([


                                            Tables\Columns\TextColumn::make('name')
                                                ->label(__('common.name'))
                                                ->iconColor('danger')
                                                ->icon('fas-tag')

                                                ->weight(FontWeight::Bold)


                                                ->sortable()
                                                ->searchable(),

                                            Tables\Columns\TextColumn::make('price')
                                                ->label(__('common.price'))

                                                ->size(TextColumnSize::Large)
                                                ->money('EUR')
                                                ->sortable(),
                                            Split::make([
                                                Tables\Columns\TextColumn::make('text')
                                                    ->color('warning')
                                                    ->default(function ($record) {
                                                        return $record->active ? __('common.active') : __('common.inactive');
                                                    }),
                                                Tables\Columns\ToggleColumn::make('active')

                                                    ->label(__('common.active')),


                                            ]),


                                            Tables\Columns\TextColumn::make('brand')
                                                ->label(__('common.brand'))

                                                ->sortable()
                                                ->searchable(),

                                        ])

                                ]),


                        ])
                    ])


            ])

            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('common.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('market_id')
                    ->label(__('common.market'))
                    ->relationship('market', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('brand')
                    ->label(__('common.brand'))
                    ->options(Product::query()->where('brand', '!=', '')->pluck('brand', 'brand')->unique())
                    ->searchable()
                    ->preload(),
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
                    //      Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            // 'create' => Pages\CreateProduct::route('/create'),
            // 'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
