<?php

namespace App\Filament\Resources\Sections\Pages;

use App\Filament\Resources\Sections\Sections\SectionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSection extends CreateRecord
{
    protected static string $resource = SectionResource::class;
}
