<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use App\Models\Section;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Stevebauman\Purify\Facades\Purify;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?int $navigationSort = 23;

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
        return __('common.category_resource_label');
    }
    public static function getPluralModelLabel(): string
    {
        return __('common.category_resource_plural_label');
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

                Forms\Components\Select::make('section_id')
                    ->label(__('common.section_resource_label'))

                    ->relationship(
                        name: 'section',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->active()->orderBy('name'),
                    )
                   ->searchable()
                    ->required()
                    ->preload()
                    ,

                Forms\Components\Textarea::make('description')
                    ->label(__('common.description'))
                    
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('image')
                    ->label(__('common.image'))
                    ->directory('images/categories')
                    ->imageEditor()
                    ->image()
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('section.name')
            ->defaultSort('name', 'asc')
            ->groups([
                Group::make('section.name')
                    ->titlePrefixedWithLabel(false)
                    //->getDescriptionFromRecordUsing(fn(Category $record): string => $record->section->description)
                    ->label(__('common.section_resource_label'))
                    ->collapsible(),



            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(function(Category $record){
                        return $record->description;
                    })
                    ->sortable()
                    ->label(__('common.name'))
                    ->searchable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label(__('common.image'))
                    ->circular()
                    ->size(50),

                Tables\Columns\ToggleColumn::make('active')
                    ->label(__('common.active'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('active')
                    ->label(__('common.active'))
                    ->query(fn(Builder $query): Builder => $query->where('active', true))
                    ->default(),
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
            'index' => Pages\ListCategories::route('/'),
            // 'create' => Pages\CreateCategory::route('/create'),
            // 'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getCleanOptionString(Model $model): string
    {
        return Purify::clean(
                view('filament.components.select-section-result')
                    ->with('name', $model?->name)
                    ->with('description', $model?->description)
                    ->with('image', $model?->image)
                    ->render()
        );
    }
}
