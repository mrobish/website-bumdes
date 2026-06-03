<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = '👤 User & Akses';

    protected static ?string $navigationLabel = 'User';

    protected static ?string $pluralModelLabel = 'User';

    protected static ?string $modelLabel = 'User';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi User')
                    ->icon('heroicon-m-user')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\Select::make('role_id')
                            ->label('Role / Jabatan')
                            ->relationship('roleDetail', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Nonaktif',
                            ])
                            ->default('active')
                            ->required(),

                        Forms\Components\TextInput::make('phone')
                            ->label('No. HP')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\FileUpload::make('avatar')
                            ->label('Foto Profil')
                            ->image()
                            ->imageEditor()
                            ->directory('avatars')
                            ->maxSize(2048)
                            ->columnSpan(2),
                    ]),

                Forms\Components\Section::make(fn (Get $get) => $get('id') ? 'Ubah Password (Kosongkan jika tidak diubah)' : 'Password Baru')
                    ->icon('heroicon-m-key')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->same('password')
                            ->dehydrated(false)
                            ->columnSpan(1),
                    ]),

                Forms\Components\Section::make('Informasi Login')
                    ->icon('heroicon-m-clock')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Placeholder::make('last_login_at')
                            ->label('Login Terakhir')
                            ->content(fn (?User $record): string => $record?->last_login_at?->diffForHumans() ?? 'Belum pernah login'),

                        Forms\Components\Placeholder::make('last_login_ip')
                            ->label('IP Terakhir')
                            ->content(fn (?User $record): string => $record?->last_login_ip ?? '-'),

                        Forms\Components\Placeholder::make('login_count')
                            ->label('Total Login')
                            ->content(fn (?User $record): string => (string) ($record?->login_count ?? 0)),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(asset('images/default-avatar.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('roleDetail.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn ($record): string => match ($record->roleDetail?->slug ?? '') {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'operator' => 'info',
                        'bendahara' => 'success',
                        'viewer' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => $state === 'active' ? 'Aktif' : 'Nonaktif'),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Login Terakhir')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Belum pernah'),

                Tables\Columns\TextColumn::make('login_count')
                    ->label('Login')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roleDetail.slug')
                    ->label('Role')
                    ->options(Role::pluck('name', 'slug')),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalDescription('Yakin ingin menghapus user ini?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
