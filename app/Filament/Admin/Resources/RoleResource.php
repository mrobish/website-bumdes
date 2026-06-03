<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = '👤 Manajemen User';

    protected static ?string $navigationLabel = 'Role & Akses';

    protected static ?string $pluralModelLabel = 'Role';

    protected static ?string $modelLabel = 'Role';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $allPermissions = Role::getAllPermissions();

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Role')
                    ->icon('heroicon-m-information-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Role')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->dehydrated()
                            ->columnSpan(1),

                        Forms\Components\Select::make('sort_order')
                            ->label('Urutan')
                            ->options(range(1, 20))
                            ->default(1)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(2)
                            ->columnSpan(2),
                    ]),

                Forms\Components\Section::make('Hak Akses (Permissions)')
                    ->icon('heroicon-m-lock-closed')
                    ->description('Centang fitur yang bisa diakses oleh role ini')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema(array_map(
                                fn ($key, $label) => Forms\Components\Checkbox::make("permissions.{$key}")
                                    ->label($label)
                                    ->default(false),
                                array_keys($allPermissions),
                                array_values($allPermissions),
                            )),
                    ]),

                Forms\Components\Placeholder::make('user_count')
                    ->label('Jumlah User')
                    ->content(fn (?Role $record): string => $record ? $record->users()->count() . ' user' : '0 user'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Role')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('User')
                    ->counts('users')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('Yakin ingin menghapus role ini?')
                    ->disabled(fn ($record) => $record->is_default),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
