<?php

namespace App\Filament\Resources\Sections\Sections;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Sections\Pages\ListSections;
use App\Filament\Resources\Sections\Pages\EditSection;
use App\Filament\Resources\SectionResource\Pages;
use App\Filament\Resources\SectionResource\RelationManagers;
use App\Filament\Resources\Sections\RelationManagers\CategoriesRelationManager;
use App\Models\Section;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;

   protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?int $navigationSort = 22;

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
        return __('common.section_resource_label');
    }
    public static function getPluralModelLabel(): string
    {
        return __('common.section_resource_plural_label');
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
                    ->relationship('market', 'name')
                    ->required(),

                Textarea::make('description')
                    ->label(__('common.description'))

                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->label(__('common.image'))
                    ->directory('images/sections')
                    ->imageEditor()
                    ->image()
                     ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('name')
                    ->description(function(Section $record){
                        return $record->description;
                    })
                    ->sortable()
                    ->label(__('common.name'))
                    ->searchable(),

                TextColumn::make('market.name')
                    ->label(__('common.market'))
                    ->searchable()
                    ->sortable(),

                ImageColumn::make('image')
                    ->label(__('common.image'))
                    ->circular()
                    ->size(50)
                    ,

                ToggleColumn::make('active')
                    ->label(__('common.active'))
                    ->sortable()
                    ,

                TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('active')
                    ->label(__('common.active'))
                    ->query(fn (Builder $query): Builder => $query->where('sections.active', true))
                    ->default(),
                 SelectFilter::make('market_id')
                    ->label(__('common.market'))
                    ->relationship('market', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                  EditAction::make()
                    ->tooltip(__('Edit'))
                    ->hiddenLabel(true),
                DeleteAction::make()
                    ->tooltip(__('Delete'))
                    ->hiddenLabel(true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CategoriesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSections::route('/'),
            //'create' => Pages\CreateSection::route('/create'),
           'edit' => EditSection::route('/{record}/edit'),
        ];
    }
}
