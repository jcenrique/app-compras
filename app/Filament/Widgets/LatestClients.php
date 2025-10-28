<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestClients extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Client::query()->latest()->take(1)
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('common.name')),
                TextColumn::make('email')
                    ->label(__('common.email')),
            ]);
    }
    protected function getTableHeading(): string
    {
        return __('common.latest_clients');
    }
}
