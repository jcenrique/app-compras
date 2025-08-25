<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Supermercado extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';


    public static function getNavigationLabel(): string
    {

        return __('Supermarket');
    }
}
