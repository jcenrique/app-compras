<?php

namespace App\Filament\Clusters\Supermercado\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Filament\Clusters\Supermercado;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Filament\Clusters\Supermercado\Resources\SectionResource\Pages;

use App\Filament\Clusters\Supermercado\Resources\SectionResource\RelationManagers\CategoryRelationManager;

class SectionResource extends Resource
{
    protected static ?string $model = Section::class;

    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Supermercado::class;

    public static function getNavigationBadgeColor(): ?string
    {
        return  'success';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getModelLabel(): string
    {
        return __('Section');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Sections');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()
                    ->label(__('Name'))
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),

                Forms\Components\Textarea::make('description'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Name')),
                Tables\Columns\TextColumn::make('description')->label(__('Description'))->limit(40, '...'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            CategoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSections::route('/'),
            // 'create' => Pages\CreateSection::route('/create'),
            'edit' => Pages\EditSection::route('/{record}/edit'),
        ];
    }
}
