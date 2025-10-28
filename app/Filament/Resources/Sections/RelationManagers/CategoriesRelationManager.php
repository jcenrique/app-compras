<?php

namespace App\Filament\Resources\Sections\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';
     public static function getModelLabel(): string
    {
        return __('common.category_resource_label');
    }
    public static function getPluralModelLabel(): string
    {
        return __('common.category_resource_plural_label');
    }

    public function form(Schema $schema): Schema
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

                Select::make('section_id')
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')

                    ->sortable()
                    ->label(__('common.name'))
                    ->searchable(),

                ImageColumn::make('image')
                    ->label(__('common.image'))
                    ->circular()
                    ->size(50),

                ToggleColumn::make('active')
                    ->label(__('common.active'))
                    ->sortable(),

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
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
