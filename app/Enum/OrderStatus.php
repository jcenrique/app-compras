<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus :int implements HasLabel, HasColor, HasIcon
{
    case PENDING =0;
 //   case PROCESSING=1;
    case COMPLETED=2;
    case CANCELED=3;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => __('common.order_statuses.pending'),
           // self::PROCESSING =>__('common.order_statuses.processing'),
            self::COMPLETED => __('common.order_statuses.completed'),
            self::CANCELED => __('common.order_statuses.canceled'),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PENDING => 'warning',
          //  self::PROCESSING => 'info',
            self::COMPLETED => 'success',
            self::CANCELED => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'heroicon-o-clock',
          //  self::PROCESSING => 'heroicon-o-cog',
            self::COMPLETED => 'heroicon-o-check-circle',
            self::CANCELED => 'heroicon-o-x-circle',
        };
    }

    public function toDatabase(): string
    {
        return strtolower($this->getLabel());
    }
}
