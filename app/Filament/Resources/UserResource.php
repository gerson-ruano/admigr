<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\FileUpload;



class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label(__('nombre'))
                ->required()
                ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                ->numeric()
                ->label(__('Telefono'))
                ->required()
                ->maxLength(255),

                Forms\Components\TextInput::make('email')
                ->label('Email direccion')
                ->email()
                ->required()
                ->maxLength(255),

                Select::make('profile')
                ->label('Rol')
                ->options([
                    '0' => 'Admin',
                    '1' => 'Employee',
                    '2' => 'Seller',
                ]),

                Select::make('status')
                ->label('Estatus')
                ->options([
                    '0' => 'Activo',
                    '1' => 'Inactivo',
                    '2' => 'Locked',
                ]),

                FileUpload::make('image')
                ->label('Imagen')
                ->image()
                ->directory('users'),
                //->required(),

                Select::make('tema')
                ->label('Tema')
                ->options([
                    '0' => 'Light',
                    '1' => 'Dark',
                ]),
               
                Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->required()
                ->maxLength(255),

                Forms\Components\TextInput::make('password_confirmation')
                ->label('Confirmar password')
                ->password()
                ->required()
                ->same('password')
                ->dehydrated(false),
            ]);
            
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()
                ->label(__('Nombre')),

                Tables\Columns\TextColumn::make('phone')
                ->label(__('Telefono')),

                Tables\Columns\TextColumn::make('email')
                ->label('Email'),

                /*
                Tables\Columns\TextColumn::make('profile')
                ->label('Rol')
                ->formatStateUsing(fn ($state) => match ($state) {
                    0 => 'Admin',
                    1 => 'Employee',
                    2 => 'Seller',
                    default => 'Desconocido',
                })
                ->badge()
                ->color(fn ($state) => match ($state) {
                    0 => 'danger',    
                    1 => 'success',   
                    2 => 'warning',   
                    default => 'secondary'
                }),*/

                TextColumn::make('profileDescription.description')->label('Perfil'),

                Tables\Columns\TextColumn::make('status')
                ->label('Estado')
                ->formatStateUsing(fn ($state) => $state == '0' ? 'Activo' : 'Inactivo')
                ->badge() 
                ->color(fn ($state) => $state == '0' ? 'success' : 'danger'),

                ImageColumn::make('image')
                ->label('Imagen')
                ->circular()
                ->getStateUsing(fn ($record) => $record->image ? asset('storage/' . $record->image) : asset('storage/noimg.jpg')), 

                Tables\Columns\TextColumn::make('tema')
                ->label('Tema')
                ->formatStateUsing(fn ($state) => $state == '1' ? 'Dark' : 'Light')
                ->badge() 
                ->color(fn ($state) => $state == '1' ? 'danger' : 'primary'),

                Tables\Columns\TextColumn::make('email_verified_at')->sortable()
                ->label(__('verificacion de email')),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
