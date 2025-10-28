<?php

namespace App\Filament\App\Resources\Sections\RelationManagers;

use App\Models\Category;
use App\Models\Section;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

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

                // Select::make('market_id')
                //     ->label(__('common.market'))
                //     ->relationship('section.market', 'name')
                // //    ->dehydrated(false)
                //     // ->afterStateUpdated(function (Set $set, $state) {
                //     //     $set('section_id', null);
                //     // })

                //     ->relationship(
                //         name: 'section.market',
                //         titleAttribute: 'name',
                //         modifyQueryUsing: fn(Builder $query) => $query->active()->orderBy('name'),
                //     )
                //     ->required()
                //     ->preload(),

                // Select::make('section_id')
                //     ->label(__('common.section_resource_label'))
                //     ->options(function (?Category $record, Get $get, Set $set) {

                //         if (! empty($record) && empty($get('market_id'))) {
                //             $set('market_id', $record->section->market_id);
                //              $set('section_id', $record->section_id);

                //         }

                //         return Section::where('market_id', $get('market_id'))->active()->orderBy('name')->pluck('name', 'id');
                //     })
                //     ->live()
                //     // ->relationship(
                //     //     name: 'section',
                //     //     titleAttribute: 'name',
                //     //     modifyQueryUsing: fn (Builder $query) => $query->active()->orderBy('name'),
                //     // )
                //     ->searchable()
                //     ->required()
                //     ->preload(),

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
                    ->description(function(Category $record){
                        return $record->description;
                    })
                    ->sortable()
                    ->label(__('common.name'))
                    ->searchable(),

                ImageColumn::make('image')
                    ->label(__('common.image'))
                    
                    ->imageSize(50),

                ToggleColumn::make('active')
                    ->label(__('common.active'))
                    ->sortable(),
            ])
            ->filters([
             //
            ])
            ->headerActions([
             CreateAction::make(),
            // AssociateAction::make(),
            ])
            ->recordActions([
             EditAction::make() 
                ->tooltip(__('common.edit'))
                ->hiddenLabel(),
           //  DissociateAction::make()->hiddenLabel(),
             DeleteAction::make()->hiddenLabel()->tooltip(__('common.delete')),
            ])
            ->toolbarActions([
             BulkActionGroup::make([
            //     DissociateBulkAction::make(),
                 DeleteBulkAction::make(),
             ]),
            ]);
    }
}
