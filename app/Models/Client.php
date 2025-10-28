<?php

namespace App\Models;


use App\Observers\ClientObserver;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Testing\Fluent\Concerns\Has;
use Spatie\Permission\Traits\HasRoles;


#[ObservedBy(ClientObserver::class)]

class Client extends Authenticatable implements FilamentUser,  MustVerifyEmail
{



    protected $guard_name = "client";
    use HasFactory,Notifiable;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'active'
    ];

    // asignar rol por defecto
     protected static function booted(): void
    {
        static::created(function (Client $client) {
            $client->assignRole('cliente');
        });
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     public function canAccessPanel(Panel $panel): bool
    {
// Permitir acceso si está en la ruta de verificación
//dd(request()->routeIs('filament.app.home'));
    // if (request()->routeIs('filament.app.auth.email-verification.prompt') || request()->routeIs('filament.app.home')) {
    //     return true;
    // }
        return true;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    //relacion con favoritos
    public function favorites(): BelongsToMany
    {
        return $this->belognsToMany(Product::class);
    }

}
