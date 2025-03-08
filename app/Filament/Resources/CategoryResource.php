<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\CatItem;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label(__('nombre'))
                ->required()
                ->maxLength(255),

                /*Select::make('status')
                ->label('Estado')
                ->options([
                    '1' => 'Activo',
                    '2' => 'Inactivo',
                    '3' => 'Locked',
                ])
                ->default('active')
                ->required(),*/

                Forms\Components\Select::make('status')
                ->label('Estado')
                ->options(
                    CatItem::where('category', 'status')
                        ->pluck('description', 'code')
                )
                ->preload()
                ->searchable()
                ->required(),

                Forms\Components\TextInput::make('slug')
                ->label(__('Slug'))
                ->required()
                ->maxLength(255),

                FileUpload::make('image')
                ->label('Imagen')
                ->image()
                ->preserveFilenames()
                ->imageEditor()
                ->directory('categories')
                ->getUploadedFileNameForStorageUsing(fn ($file) => time() . '_' . $file->getClientOriginalName())
                //->getStateUsing(fn ($record) => $record?->image ? asset('storage/' . $record->image) : null)
                ]);
                      
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()
                ->label(__('Nombre')),

                TextColumn::make('statusDescription.description')->label('Estado'),
 
                Tables\Columns\TextColumn::make('slug')->searchable()
                ->label(__('Slug')),
                
                ImageColumn::make('image')
                ->label('Imagen')
                ->size(40)
                ->extraAttributes(['style' => 'border-radius: 8px; object-fit: cover;'])
                ->getStateUsing(fn ($record) => $record->image ? asset('storage/' . $record->image) : asset('storage/noimg.jpg')),
                //->url(fn ($record) => asset('storage/' . $record->image)),
                
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}