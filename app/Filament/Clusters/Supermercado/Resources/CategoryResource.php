<?php

namespace App\Filament\Clusters\Supermercado\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Section;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use App\Filament\Clusters\Supermercado;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Supermercado\Resources\CategoryResource\Pages;
use App\Filament\Clusters\Supermercado\Resources\CategoryResource\RelationManagers;
use App\Filament\Clusters\Supermercado\Resources\CategoryResource\RelationManagers\ProductsRelationManager;
use App\Filament\Clusters\Supermercado\Resources\SectionResource\RelationManagers\CategoryRelationManager;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $cluster = Supermercado::class;

    protected static ?int $navigationSort = 4;

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
        return __('Category');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Categories');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('section_id')
                    ->label(__('Sección'))
                    ->options(Section::all()->pluck('name', 'id'))
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Name'))
                    ->searchable(),
                // Tables\Columns\TextColumn::make('slug')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('section.name')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('section.name')
                    ->label(__('Sections'))
                    ->getDescriptionFromRecordUsing(fn(Category $record): string => $record->section->description)
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            ])
            ->defaultGroup('section.name')
            ->groupingSettingsHidden()
            ->filters([
                //
            ])
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
            'index' => Pages\ListCategories::route('/'),
            //'create' => Pages\CreateCategory::route('/create'),
            //'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
