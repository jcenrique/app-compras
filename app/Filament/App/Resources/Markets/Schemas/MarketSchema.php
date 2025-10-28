<?php

namespace App\Filament\App\Resources\Markets\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MarketSchema
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
                    ->inLine(false)
                    ->required(),

                FileUpload::make('logo')
                    ->label(__('common.logo'))
                    ->directory('images/logos')
                    ->imageEditor()
                    ->image()
                    ->columnSpanFull(),

            ]);
    }
}
