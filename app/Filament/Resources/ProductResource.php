<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label(__('nombre'))
                ->required()
                ->maxLength(255),

                Forms\Components\TextInput::make('description')
                ->label(__('Descripción'))
                ->required()
                ->maxLength(255),

                Forms\Components\TextInput::make('price')
                ->label(__('Precio'))
                ->required()
                ->maxLength(255),

                FileUpload::make('image')
                ->label('Imagen')
                ->image()
                ->directory('products')
                ->required(),

                Select::make('category_id')
                ->label('Categoría')
                ->relationship('category', 'name')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')->searchable()
                ->label(__('Descripciòn')),
                Tables\Columns\TextColumn::make('name')->searchable()
                ->label(__('Nombre')),
                Tables\Columns\TextColumn::make('price')->searchable()
                ->label(__('Precio')),
                ImageColumn::make('image')
                ->label('Imagen')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
