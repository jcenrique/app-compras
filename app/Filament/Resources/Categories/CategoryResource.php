<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\Tables\CategoriesTable;
use App\Models\Category;
use App\Models\Section;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?int $navigationSort = 23;

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
        return __('common.category_resource_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.category_resource_plural_label');
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

                Select::make('market_id')
                    ->label(__('common.market'))
                    ->relationship('section.market', 'name')
                    ->dehydrated(false)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('section_id', null);
                    })

                    ->relationship(
                        name: 'section.market',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->active()->orderBy('name'),
                    )
                    ->required()
                    ->preload(),

                Select::make('section_id')
                    ->label(__('common.section_resource_label'))
                    ->options(function (?Category $record, Get $get, Set $set) {

                        if (! empty($record) && empty($get('market_id'))) {
                            $set('market_id', $record->section->market_id);
                             $set('section_id', $record->section_id);

                        }

                        return Section::where('market_id', $get('market_id'))->active()->orderBy('name')->pluck('name', 'id');
                    })
                    ->live()
                    // ->relationship(
                    //     name: 'section',
                    //     titleAttribute: 'name',
                    //     modifyQueryUsing: fn (Builder $query) => $query->active()->orderBy('name'),
                    // )
                    ->searchable()
                    ->required()
                    ->preload(),

                Textarea::make('description')
                    ->label(__('common.description'))

                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label(__('common.image'))
                    ->directory('images/categories')
                    ->imageEditor()
                    ->image()
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
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
            'index' => ListCategories::route('/'),
            // 'create' => Pages\CreateCategory::route('/create'),
            // 'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
