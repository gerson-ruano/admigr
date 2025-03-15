<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DispatchResource\Pages;
use App\Filament\Resources\DispatchResource\RelationManagers;
use App\Models\Dispatch;
use App\Models\User;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use function Laravel\Prompts\multiselect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;



class DispatchResource extends Resource
{
    protected static ?string $model = Dispatch::class;
    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->default('ORD-' . now()->format('Hi-dmY'))
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),
                        /*Forms\Components\Select::make('user_id')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Customer'),*/

                        Forms\Components\Select::make('user_id')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('profile')
                                    ->options([
                                        'customer' => 'customer',  
                                    ])
                                    ->default('customer')  
                                    ->hidden()  
                                    ->required()
                                    ->label('Customer'),

                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required()
                                    ->maxLength(255),

                            ])
                            ->required(),
                        Forms\Components\Select::make('seller_id')
                            ->relationship('seller', 'name', function (Builder $query) {
                                return $query->role('seller');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Seller'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535),
                    ])->columnSpan(1),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            if ($product) {
                                                $set('unit_price', $product->price);
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        $unitPrice = $get('unit_price');
                                        $set('subtotal', $unitPrice * $state);
                                    }),
                                Forms\Components\TextInput::make('unit_price')
                                    ->numeric()
                                    ->prefix('Q')
                                    ->disabled()
                                    ->dehydrated()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        $quantity = $get('quantity');
                                        $set('subtotal', $state * $quantity);
                                    }),
                                Forms\Components\TextInput::make('subtotal')
                                    ->numeric()
                                    ->prefix('Q')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                            ])
                            ->defaultItems(1)
                            ->columns(2)
                            ->columnSpan(2)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $totalAmount = collect($state)->sum('subtotal');
                                $set('total_amount', $totalAmount);
                            }),

                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('Q')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                    ])->columnSpan(2),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seller.name')
                    ->searchable()
                    ->sortable()
                    ->label('Seller'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('gtq')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'processing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer')
                    ->relationship('customer', 'name'),
                Tables\Filters\SelectFilter::make('seller')
                    ->relationship('seller', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                /*Tables\Actions\BulkActionGroup::make([
                    
                ]),*/
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDispatches::route('/'),
            'create' => Pages\CreateDispatch::route('/create'),
            'edit' => Pages\EditDispatch::route('/{record}/edit'),
        ];
    }


}
