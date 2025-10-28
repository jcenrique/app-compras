<?php

namespace App\Filament\Resources\SectionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
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

    public function form(Form $form): Form
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    
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
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
