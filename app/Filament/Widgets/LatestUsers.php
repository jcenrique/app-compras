<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestUsers extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table

            ->query(
                \App\Models\User::query()->latest()->take(1)


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
        return __('common.latest_users');
    }




}
