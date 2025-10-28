<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CategoryResource\Pages;
use App\Filament\App\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
/**
 * Import the Filter class from the Filament Tables Filters namespace.
 * This class is used to define custom filters for Filament tables.
 */
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    // Icono de navegación en el panel de administración
    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    // Orden de navegación en el menú
    protected static ?int $navigationSort = 5;

    // Grupo de navegación donde se mostrará este recurso
    public static function getNavigationGroup(): ?string
    {
        return __('common.market_management_nav_group');
    }

    // Color de la insignia de navegación
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    // Valor de la insignia de navegación (cantidad de registros)
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    // Etiqueta singular del modelo
    public static function getModelLabel(): string
    {
        return __('common.category_resource_label');
    }

    // Etiqueta plural del modelo
    public static function getPluralModelLabel(): string
    {
        return __('common.category_resource_plural_label');
    }

    // Define el formulario para crear/editar categorías
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label(__('common.name'))
                ->required()
                ->maxLength(255),
            Forms\Components\Toggle::make('active')
                ->label(__('common.active'))
                ->default(true)
                ->inline(false)
                ->required(),
            Forms\Components\Textarea::make('description')
                ->label(__('common.description'))
                ->required()
                ->columnSpanFull(),
            Forms\Components\FileUpload::make('image')
                ->label(__('common.image'))
                ->directory('images/categories')
                ->imageEditor()
                ->image()
                ->columnSpanFull(),
        ]);
    }

    // Define la tabla de listado de categorías
    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultPaginationPageOption(25)
            ->defaultGroup('section.name')
            ->defaultSort('name', 'asc')
            ->groups([
                Group::make('section.name')
                    ->titlePrefixedWithLabel(false)
                    ->label(__('common.section_resource_label'))
                    ->collapsible(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(function(Category $record) {
                        return $record->description;
                    })
                    ->label(__('common.name'))
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('common.image'))
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
                // Filtro para mostrar solo categorías activas
                Filter::make('active')
                    ->label(__('common.active'))
                    ->query(fn(Builder $query): Builder => $query->where('active', true))
                    ->default(),
            ])
            ->hiddenFilterIndicators()
            ->actions([
                // Acción para editar una categoría
                Tables\Actions\EditAction::make()
                    ->tooltip(__('Edit'))
                    ->hiddenLabel(true),
                // Acción para eliminar una categoría
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

    // Define las relaciones del recurso (vacío en este caso)
    public static function getRelations(): array
    {
        return [];
    }

    // Define las páginas disponibles para este recurso
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            // 'create' => Pages\CreateCategory::route('/create'),
            // 'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
