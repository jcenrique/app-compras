<?php

namespace App\Filament\App\Resources\Sections\Resources\Categories;

use App\Filament\App\Resources\Sections\Resources\Categories\Pages\CreateCategory;
use App\Filament\App\Resources\Sections\Resources\Categories\Pages\EditCategory;
use App\Filament\App\Resources\Sections\Resources\Categories\Schemas\CategoryForm;
use App\Filament\App\Resources\Sections\Resources\Categories\Tables\CategoriesTable;
use App\Filament\App\Resources\Sections\SectionResource;
use App\Models\Category;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = SectionResource::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
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
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
