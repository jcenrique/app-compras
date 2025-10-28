<?php

namespace App\Filament\Resources\Clients\Clients;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

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

                Toggle::make('active')
                    ->inline(false),

              Select::make('roles')
                    ->label(__('filament-shield::filament-shield.resource.label.roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                TextInput::make('password')
                    ->password()
                    ->hiddenOn('edit')
                    ->required()
                    ->maxLength(255),

             
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


               ToggleColumn::make('active')
                    ->label(__('common.active'))
                    ->sortable()
                    ,
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
                //
            ])
            ->recordActions([
                 EditAction::make()
                    ->tooltip(__('Edit'))
                    ->hiddenLabel(true),
                DeleteAction::make()
                    ->tooltip(__('Delete'))
                    ->hiddenLabel(true),
                    Action::make(__('common.resend_verification_email'))
                    ->tooltip(__('common.resend_verification_email'))
                    ->hiddenLabel(true)
                    ->icon('heroicon-o-envelope')
                    ->authorize(fn(Client $record) => !$record->hasVerifiedEmail())
                    ->action(function (Client $record) {
                        $notification = new VerifyEmail();
                        $notification->url = filament()->getPanel('app')->getVerifyEmailUrl($record);

                        $record->notifyNow($notification);

                        Notification::make()
                            ->title(__('common.verification_email_resent'))
                            ->success()
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            //'create' => Pages\CreateClient::route('/create'),
           // 'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
