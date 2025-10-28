<?php

namespace App\Filament\Resources\Products\Products;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Category;
use App\Models\Product;
<<<<<<< HEAD:app/Filament/Resources/Products/Products/ProductResource.php
use App\Models\Section;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
=======
use App\Tables\Columns\MarKetColumn;
use Darryldecode\Cart\Facades\CartFacade;
use Filament\Forms;
use Filament\Forms\Components\Grid as ComponentsGrid;
use Filament\Forms\Form;
use Filament\Forms\Set;
>>>>>>> 6b1376f85b479673c4aa818bd317a135a66d9e7d:app/Filament/App/Resources/ProductResource.php
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 24;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('common.market_management_nav_group');
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('common.name'))
                    ->required()
                    ->maxLength(255),
                Toggle::make('active')
                    ->label(__('common.active'))
                    ->default(true)
                    ->inline(false)
                    ->required(),

                RichEditor::make('description')

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

                Select::make('market_id')
                    ->label(__('common.market'))
                    ->relationship('category.section.market', 'name')
                    ->dehydrated(false)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('section_id', null);
                    })

                    ->relationship(
                        name: 'category.section.market',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->active()->orderBy('name'),
                    )
                    ->required()
                    ->preload(),

                Select::make('section_id')
                    ->live()
                    ->label(__('common.section_resource_label'))
                    ->dehydrated(false)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('category_id', null);
                    })
                    ->options(function (?Product $record, Get $get, Set $set) {

                        if (! empty($record) && empty($get('market_id'))) {
                            $set('market_id', $record->category->section->market_id);
                            $set('section_id', $record->category->section_id);

                        }

                        return Section::where('market_id', $get('market_id'))->active()->orderBy('name')->pluck('name', 'id');
                    })

                    ->searchable()
                    ->required()
                    ->preload(),
                Select::make('category_id')
                    ->label(__('common.category'))
                    ->options(function (?Product $record, Get $get, Set $set) {
                        if (! empty($record) && empty($get('section_id'))) {
                            $set('section_id', $record->category->section_id);
                            $set('category_id', $record->category_id);
                            $set('market_id', $record->category->section->market_id);
                        }

                        return Category::where('section_id', $get('section_id'))->active()->orderBy('name')->pluck('name', 'id');
                    })
                    ->live()
                    ->searchable()
                    ->required()
                    ->preload(),

                FileUpload::make('image')
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

                        // return '';

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
                                    ImageColumn::make('image')
                                        ->alignment(Alignment::Center)
                                        ->imageSize(150),
                                ])->grow(false),

                            Grid::make()
                                ->columns(1)
                                ->schema([

                                    TextColumn::make('category.name')
                                        ->color('gray')
                                        ->prefix(__('common.category').': ')
                                        ->hidden(function (Table $table) {
                                            if ($table->getGrouping() && $table->getGrouping()->getId() === 'category.name') {
                                                return true;
                                            }
                                        })
                                        ->label(__('common.category'))
                                        ->sortable(),
                                    // columna para marcar favoritos
                                    Split::make([
                                        TextColumn::make('text1')
                                            ->color('warning')
                                            ->default(function ($record) {
                                                return __('common.is_favorite');
                                            }),

                                    ]),

                                    Grid::make()
                                        ->columns(1)
                                        ->schema([

                                            TextColumn::make('name')
                                                ->label(__('common.name'))
                                                ->iconColor('danger')
                                                ->icon('fas-tag')

                                                ->weight(FontWeight::Bold)

                                                ->sortable()

                                                ->searchable(query: function (Builder $query, string $search): Builder {
                                                    return $query
                                                        ->where('products.name', 'like', "%{$search}%");

                                                }),
                                            TextColumn::make('format')
                                                ->label(__('common.format')),

                                            TextColumn::make('price')
                                                ->label(__('common.price'))

                                                ->size(TextSize::Large)
                                                ->money('EUR')
                                                ->sortable(),
                                            Split::make([
                                                TextColumn::make('text')
                                                    ->color('warning')
                                                    ->default(function ($record) {
                                                        return $record->active ? __('common.active') : __('common.inactive');
                                                    }),
                                                ToggleColumn::make('active')

                                                    ->label(__('common.active')),

                                            ]),

                                            TextColumn::make('brand')
                                                ->label(__('common.brand'))

                                                ->sortable()
                                                ->searchable(query: function (Builder $query, string $search): Builder {
                                                    return $query
                                                        ->where('products.brand', 'like', "%{$search}%");

                                                }),
                                        ]),

                                ]),

                        ]),
                    ]),

            ])

            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('common.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('market_id')
                    ->label(__('common.market'))
                    ->relationship('market', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('brand')
                    ->label(__('common.brand'))
                    ->options(Product::query()->where('brand', '!=', '')->pluck('brand', 'brand')->unique())
                    ->searchable()
                    ->preload(),
            ])
<<<<<<< HEAD:app/Filament/Resources/Products/Products/ProductResource.php
            ->recordActions([
                EditAction::make()
                    ->tooltip(__('common.edit'))
=======
            ->actions([
                Tables\Actions\Action::make('add_to_cart')
                    ->tooltip(__('common.add_to_cart'))
                    ->action(function (Model $record) {
                        //añadir el producto al carrito de la compra usando darryldecode/laravelshoppingcart
                        $cart = CartFacade::session(Auth::id());
                        $cart->add(
                            $record->id,
                            $record->name,
                            $record->price,
                            1,
                            [
                                'attributes' => [],
                                'associatedModel' => $record
                            ]
                        );

                      
                    })
                    ->hiddenLabel(true)
                    ->icon('fas-cart-plus')
                    ->color('success'),

                Tables\Actions\EditAction::make()
                    ->tooltip(__('Edit'))
>>>>>>> 6b1376f85b479673c4aa818bd317a135a66d9e7d:app/Filament/App/Resources/ProductResource.php
                    ->hiddenLabel(true),
                DeleteAction::make()
                    ->tooltip(__('common.delete'))
                    ->hiddenLabel(true),
<<<<<<< HEAD:app/Filament/Resources/Products/Products/ProductResource.php
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //      Tables\Actions\DeleteBulkAction::make(),
=======
            ])->actionsAlignment('right')->actionsPosition(ActionsPosition::BeforeColumns)
           
            ->headerActions([
                Tables\Actions\Action::make('view_cart')
                    ->label(function () {
                        $cart = CartFacade::session(Auth::id());
                        $count = $cart->getContent()->count();
                        return new HtmlString('<span class="relative inline-flex">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2">' . $count . '</span>
                      </span>');
                    })
                    ->action(function () {
                        $cart = CartFacade::session(Auth::id());
                        $cartItems = $cart->getContent();
                        dd($cartItems);
                    })
                     ->tooltip(__('common.view_cart'))
                  
                    //->icon('fas-shopping-cart')
                    ->color('primary'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
>>>>>>> 6b1376f85b479673c4aa818bd317a135a66d9e7d:app/Filament/App/Resources/ProductResource.php
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
            'index' => ListProducts::route('/'),
            // 'create' => Pages\CreateProduct::route('/create'),
            // 'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
