<?php

namespace App\Filament\App\Resources\Sections\Tables;

use App\Models\Section;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SectionsTable
{
    public static function configure(Table $table): Table
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
                    
                    ->imageSize(50)
                    ,

                ToggleColumn::make('active')
                    ->label(__('common.active'))
                    ->sortable()
                    ,

               
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
                   // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
