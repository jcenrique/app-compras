<?php

namespace App\Filament\Resources\Users;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;

use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
//use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

 protected static ?int $navigationSort = 11;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function getNavigationGroup(): ?string
    {
        return __('common.user_management_nav_group');
    }

    public static function getModelLabel(): string
    {
        return __('common.user_resource_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.user_resource_plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('common.name'))

                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('common.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                DateTimePicker::make('email_verified_at')
                    ->label(__('common.email_verified_at'))
                    ->hiddenOn('edit'),
                TextInput::make('password')
                    ->label(__('common.password'))
                    ->password()
                    ->required()
                    ->hiddenOn('edit')
                    ->maxLength(255),

                Select::make('roles')
                    ->label(__('filament-shield::filament-shield.resource.label.roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('common.name'))

                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('common.email'))
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->label(__('common.email_verified_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

                TernaryFilter::make('verified')
                    ->label('Verified email')
                    ->attribute('email_verified_at')
                    ->nullable(),
            ])
            ->recordActions([
                 EditAction::make()
                    ->tooltip(__('Edit'))
                    ->hiddenLabel(true),
                DeleteAction::make()
                    ->tooltip(__('Delete'))
                    ->hiddenLabel(true),

                Action::make('resend_verification_email')
                    ->tooltip((__('common.Resend Verification Email')))
                    ->hiddenLabel(true)
                    ->icon('heroicon-o-envelope')
                    ->authorize(fn(User $record) => !$record->hasVerifiedEmail())
                    ->action(function (User $record) {
                        $notification = new VerifyEmail();
                        $notification->url = filament()->getVerifyEmailUrl($record);

                        $record->notifyNow($notification);

                        Notification::make()
                            ->title("Verification email has been resent.")
                            ->send();
                    })
                    ->requiresConfirmation(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
          //  AuditsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
