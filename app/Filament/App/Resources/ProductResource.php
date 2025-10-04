<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ProductResource\Pages;
use App\Filament\App\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Product;
use App\Tables\Columns\MarKetColumn;
use Darryldecode\Cart\Facades\CartFacade;
use Filament\Forms;
use Filament\Forms\Components\Grid as ComponentsGrid;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 3;

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
       
        return Favorite::where('client_id', Auth::id())->get()->count();
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
                    ->columnSpanFull()
                    ->maxLength(255),

                Forms\Components\Toggle::make('active')
                    ->label(__('common.active'))
                    ->onIcon('fas-circle-check')
                    ->offIcon('fas-ban')
                    ->default(true)
                    ->inline(false)
                    ->required(),

                //marcar el producto como favorito para el cliente logeado
                Forms\Components\Toggle::make('is_favorite')
                    
                    ->label(__('common.is_favorite'))
                    ->afterStateHydrated(function (Forms\Components\Toggle $component , ?Product $record) {
                       if($record){
                            $component->state($record->favorites()->where('client_id', Auth::id())->exists());
                       }else{
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




                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull(),

                Forms\Components\Select::make('market_id')
                    ->label(__('common.market'))

                    ->relationship(
                        name: 'market',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->active()->orderBy('name'),
                    )
                    ->searchable()
                    ->required()
                    ->preload(),

                Forms\Components\TextInput::make('price')
                    ->label(__('common.price'))
                    ->default(0)
                    ->required()
                    ->numeric()
                    ->prefix('€'),




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

                Forms\Components\TextInput::make('brand')
                    ->label(__('common.brand'))
                    ->maxLength(255),

                Forms\Components\FileUpload::make('image')
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

            //  ->striped()

            ->deferLoading()
            ->paginated()
            ->defaultPaginationPageOption(25)
               ->defaultGroup('category.name')
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
                                        Tables\Columns\ToggleColumn::make('is_favorite')
                                            ->onColor('success')
                                            ->label(__('common.is_favorite'))
                                            ->tooltip(__('common.is_favorite'))
                                            ->updateStateUsing(function ($record, $state) {
                                                if ($state) {
                                                    $record->favorites()->attach(Auth::id());
                                                 
                                                } else {
                                                    $record->favorites()->detach(Auth::id());
                                               
                                                }
                                            })
                                            ->getStateUsing(function ($record) {
                                               
                                                return $record->favorites()->where('client_id', Auth::id())->exists();
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
                //filtro para favoritos
                Tables\Filters\Filter::make('is_favorite')
                    ->label(__('common.is_favorite'))
                    ->toggle()
                    ->default()
                    ->query(fn(Builder $query, array $data): Builder => $query->whereHas('favorites', fn($q) => $q->where('client_id', Auth::id()))),

                Tables\Filters\Filter::make('active')
                    ->toggle()
                    ->label(__('common.active'))
                    ->query(fn(Builder $query, array $data): Builder => $query->active()),

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
                    ->hiddenLabel(true),
                Tables\Actions\DeleteAction::make()
                    ->tooltip(__('Delete'))
                    ->hiddenLabel(true),
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
    //modificar elpquent query para incluir los productos favoritos
    public static function getEloquentQuery(): Builder
    {
        return  parent::getEloquentQuery()->with(['favorites' => function ($q) {
            $q->where('client_id', Auth::id());
        }]);
    }
}
