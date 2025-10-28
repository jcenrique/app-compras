<?php

namespace App\Filament\App\Resources\Products;

use App\Filament\App\Resources\Products\Pages\ListProducts;
use App\Models\Category;
use App\Models\Product;
use App\Models\Section;
use Darryldecode\Cart\Facades\CartFacade;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 40;

    public static function getNavigationGroup(): ?string
    {
        return __('common.market_management_nav_group');
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function getNavigationBadge(): ?string
    {

        return Product::all()->count();
    }

    // tooltip badge
    public static function getNavigationBadgeTooltip(): ?string
    {
        return Str::plural(__('common.is_favorite'));
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
                    ->columnSpanFull()
                    ->maxLength(255),

                Toggle::make('active')
                    ->label(__('common.active'))
                    ->onIcon('fas-circle-check')
                    ->offIcon('fas-ban')
                    ->default(true)
                    ->inline(false)
                    ->required(),

                // marcar el producto como favorito para el cliente logeado
                Toggle::make('is_favorite')

                    ->label(__('common.is_favorite'))
                    ->afterStateHydrated(function (Toggle $component, ?Product $record) {
                        if ($record) {
                            $component->state($record->favorites()->where('client_id', Auth::id())->exists());
                        } else {
                            $component->state(true);
                        }
                    })

                    ->afterStateUpdated(function ($state, Product $record) {

                        if ($state) {
                            $record->favorites()->syncWithoutDetaching([Auth::id()]);
                        } else {
                            $record->favorites()->detach(Auth::id());
                        }
                    })
                    ->onColor('success')
                    ->onIcon('fas-bookmark')
                    ->offIcon('fas-rectangle-xmark')
                    ->inline(false),

                RichEditor::make('description')
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label(__('common.price'))
                    ->default(0)
                    ->required()
                    ->numeric()
                    ->prefix('€'),

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

                TextInput::make('brand')
                    ->label(__('common.brand'))
                    ->maxLength(255),

                FileUpload::make('image')
                    ->columnSpanFull()
                    ->label(__('common.image'))
                    ->directory('images/products')
                    ->imageEditor()
                    ->image(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->deferLoading()
        
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->withAggregate('category', 'name')
                    ->orderBy('category_name')
                    ->orderBy('name');
            })

            ->poll(5)
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
                                        ->alignCenter()
                                        ->alignment(Alignment::Center)
                                        ->imageSize(150),
                                ])->grow(false),

                            Grid::make()
                                ->columns(1)
                                ->schema([

                                    TextColumn::make('category.name')
                                        ->color('primary')
                                        // ->prefix(__('common.category') . ': ')
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
                                        ToggleColumn::make('is_favorite')
                                            ->onColor('success')
                                            ->label(__('common.is_favorite'))
                                            ->tooltip(__('common.is_favorite'))
                                            ->updateStateUsing(function ($record, $state, Component $livewire) {
                                                if ($state) {
                                                    $record->favorites()->attach(Auth::id());
                                                } else {
                                                    $record->favorites()->detach(Auth::id());
                                                }
                                                // refrescar el badge del carrito en la barra lateral
                                                $livewire->dispatch('refresh-sidebar');
                                            })
                                            ->getStateUsing(function ($record) {

                                                return $record->favorites()->where('client_id', Auth::id())->exists();
                                            }),

                                    ]),

                                    Grid::make()
                                        ->columns(1)
                                        ->schema([

                                            TextColumn::make('name')
                                                ->label(__('common.name'))
                                                ->iconColor('danger')
                                                ->icon('fas-tag')
                                                ->tooltip(function ($record) {
                                                    return $record->name;
                                                })
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
            ->recordActions([
                Action::make('add_to_cart')
                    ->tooltip(__('common.add_to_cart'))
                    ->action(function (Model $record, Component $livewire) {
                        $cart = CartFacade::session(Auth::id());
                        // comprobar si hay productos en el carrito y que son sel mismo supermercado

                        $cartItems = $cart->getContent();
                        // comprobar que hay productos en el carrito
                        if ($cartItems->count() > 0) {
                            $market_id_new = $record->market_id;
                            $market_id_in_cart = $cartItems->first()->attributes->associatedModel->market_id;
                            // comprobar que los productos del carrito son de lmismo supermercado
                            if ($market_id_new != $market_id_in_cart) {

                                Notification::make()
                                    ->title(__('common.diferent_market_product'))
                                    ->body(__('common.product_not_add_to_cart'))
                                    ->danger()
                                    ->seconds(10)
                                    ->send();

                                return;

                            }
                        }

                        // añadir el producto al carrito de la compra usando darryldecode/laravelshoppingcart

                        $cart->add(
                            $record->id,
                            $record->name,

                            $record->price,
                            1,
                            [
                                'attributes' => [],
                                'associatedModel' => $record,
                            ]
                        );
                        // poner el producto como favorito si no esta
                        $record->favorites()->attach(Auth::id());

                        // refrescar el badge del carrito en la barra lateral
                        $livewire->dispatch('refresh-sidebar');
                    })
                    ->hiddenLabel(true)
                    ->icon('fas-cart-plus')
                    ->color('success'),
                EditAction::make()
                    ->tooltip(__('common.edit'))
                    ->hiddenLabel(true),
                DeleteAction::make()
                    ->tooltip(__('common.delete'))
                    ->successRedirectUrl(route('filament.app.resources.products.index'))
                    ->hiddenLabel(true),
            ])->recordActionsPosition(RecordActionsPosition::BeforeColumns)->recordActionsAlignment(Alignment::End->value)
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
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

    // modificar elpquent query para incluir los productos favoritos
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['favorites' => function ($q) {
            $q->where('client_id', Auth::id());
        }]);
    }
}
