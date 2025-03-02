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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label(__('filament-forms::form.name'))
                ->required()
                ->maxLength(255),
                Forms\Components\TextInput::make('email')
                ->label('Email address')
                ->email()
                ->required()
                ->maxLength(255),
                Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->required()
                ->maxLength(255),
                Forms\Components\TextInput::make('password_confirmation')
                ->label('Confirm password')
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
                Tables\Columns\TextColumn::make('name')->searchable(),
                //->label(__('form.name')),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('email_verified_at')->sortable(),
                Tables\Columns\TextColumn::make('acciones')
                    ->label('Acciones')
                    ->sortable(false)
                    ->default('') // Evita que se muestre contenido en la columna
                    ->extraAttributes(['class' => 'text-center font-bold']), 
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
