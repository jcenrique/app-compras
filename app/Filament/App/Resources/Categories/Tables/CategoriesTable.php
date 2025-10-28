<?php

namespace App\Filament\App\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriesTable
{
    public static function configure(Table $table): Table
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
                TextColumn::make('name')
                    ->description(function(Category $record){
                        return $record->description;
                    })
                    ->sortable()
                    ->label(__('common.name'))
                    ->searchable(),

                ImageColumn::make('image')
                    ->label(__('common.image'))
                    
                    ,

                ToggleColumn::make('active')
                    ->label(__('common.active'))
                    ->sortable(),

               
            ])
            ->filters([
                Filter::make('category.active')
                    ->label(__('common.active'))
                    ->query(fn(Builder $query): Builder => $query->where('categories.active', true))
                    ->default(),

                 SelectFilter::make('market_id')
                    ->label(__('common.market'))
                    ->relationship('section.market', 'name')
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
}
