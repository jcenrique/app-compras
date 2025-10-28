<?php

namespace App\Filament\App\Resources\Sections\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;

class SectionForm
{
    public static function configure(Schema $schema): Schema
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
                    ->hiddenOn(Operation::Edit)
                    ->relationship('market', 'name')
                    ->required(),

                TextEntry::make('market.name')
                    ->color('primary')
                    ->icon('fas-building-un')
                    ->hiddenOn(Operation::Create)
                    ->label(__('common.market')),
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
}
