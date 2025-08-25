<?php

namespace App\Enum;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusShopping: string implements HasLabel, HasColor, HasIcon
{
    case PENDING = 'pending';
    case FINISHED = 'finished';



    public function getLabel(): ?string
    {

        return match ($this) {
            self::PENDING =>__('Pending'),
            self::FINISHED => __('Finished'),

        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {

            self::PENDING => 'warning',
            self::FINISHED => 'success',

        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {

            self::PENDING => 'heroicon-m-hand-raised',
            self::FINISHED => 'heroicon-m-check',
        };
    }

}
