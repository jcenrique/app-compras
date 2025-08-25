<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

 protected static ?int $navigationSort = 31;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function getNavigationGroup(): ?string
    {
        return __('common.clients_nav_group');
    }

    public static function getModelLabel(): string
    {
        return __('common.client_resource_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('common.client_resource_plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('common.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__('common.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\Toggle::make('active')
                    ->inline(false),

                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->label(__('common.email_verified_at'))
                    ->hiddenOn('edit'),



                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('roles')
                    ->label(__('filament-shield::filament-shield.resource.label.roles'))
                    ->relationship(
                        name: 'roles',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->where('guard_name', 'client'),
                    )
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('common.name'))

                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('common.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label(__('common.email_verified_at'))
                    ->dateTime()
                    ->sortable(),


               Tables\Columns\ToggleColumn::make('active')
                    ->label(__('common.active'))
                    ->sortable()
                    ,
               Tables\Columns\TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                 Tables\Actions\EditAction::make()
                    ->tooltip(__('Edit'))
                    ->hiddenLabel(true),
                Tables\Actions\DeleteAction::make()
                    ->tooltip(__('Delete'))
                    ->hiddenLabel(true),
                    Tables\Actions\Action::make('resend_verification_email')
                    ->tooltip((__('common.Resend Verification Email')))
                    ->hiddenLabel(true)
                    ->icon('heroicon-o-envelope')
                    ->authorize(fn(Client $record) => !$record->hasVerifiedEmail())
                    ->action(function (Client $record) {
                        $notification = new VerifyEmail();
                        $notification->url = filament()->getVerifyEmailUrl($record);

                        $record->notifyNow($notification);

                        Notification::make()
                            ->title("Verification email has been resent.")
                            ->send();
                    })
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            //'create' => Pages\CreateClient::route('/create'),
           // 'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
