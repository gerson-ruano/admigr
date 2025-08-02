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

use Filament\Notifications\Notification;
use Filament\Forms\Components\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Pages\Actions\Action;



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
                                    //->required()
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
                                //return $query->role('seller');
                                return $query->whereHas('roles', fn($q) => $q->where('name', 'seller'));
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Seller'),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(fn($record) => $record?->getAvailableStatuses() ?? [
                                'pending' => 'Pendiente',
                                'processing' => 'Procesando',
                                'completed' => 'Completado',
                                'cancelled' => 'Cancelado',
                            ])
                            ->disabled(fn($record) => in_array($record?->status, ['completed', 'cancelled']))
                            ->required(),
                        //->default('pending')

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
                                    ->label('Producto')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $get, $component) {
                                        if (!$state)
                                            return;

                                        $items = $get('../../items') ?? [];

                                        // Obtener la lista de productos ya seleccionados, excluyendo este
                                        $currentPath = $component->getStatePath();
                                        $currentKey = str($currentPath)->beforeLast('.')->after('items.')->toString();

                                        /*dd([
                                            'currentIndex' => $currentPath,
                                            'currentkey' => $currentKey,
                                            'itemIndices' => array_keys($items),
                                            'items' => $items,
                                        ]);*/

                                        $isDuplicate = collect($items)
                                            ->filter(function ($item, $key) use ($state, $currentKey) {
                                                return $key !== $currentKey && ($item['product_id'] ?? null) == $state;
                                            })
                                            ->isNotEmpty();

                                        if ($isDuplicate) {
                                            $set('product_id', null);

                                            Notification::make()
                                                ->title('Producto ya ingresado')
                                                ->body('Este producto ya ha sido seleccionado en otro ítem.')
                                                ->danger()
                                                ->send();
                                            return;
                                        }

                                        // Continuar con lógica de precio y subtotal si no hay duplicado
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('unit_price', $product->price);
                                            $quantity = $get('quantity') ?? 1;
                                            $set('subtotal', $product->price * $quantity);

                                            // Actualizar total general
                                            $newItems = collect($get('../../items') ?? []);
                                            $total = $newItems->pluck('subtotal')->filter()->sum();
                                            $set('../../total_amount', $total);
                                        }
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $unitPrice = $get('unit_price') ?? 0;
                                        $set('subtotal', $unitPrice * $state);

                                        $items = $get('../../items') ?? [];
                                        $total = collect($items)->pluck('subtotal')->filter()->sum();
                                        $set('../../total_amount', $total);
                                    }),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Precio unitario')
                                    ->numeric()
                                    ->prefix('Q')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Q')
                                    ->disabled()
                                    ->dehydrated()
                                    ->live(),

                            ])
                            ->defaultItems(1)
                            ->columns(2)
                            ->columnSpan(2)
                            ->reactive()
                            ->live(),

                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total')
                            ->numeric()
                            ->prefix('Q')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->live(),

                    ])->columnSpan(2)
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
            
            Tables\Actions\Action::make('imprimir_factura')
                ->label('Factura')
                ->icon('heroicon-o-printer')
                ->url(fn ($record) => route('factura.dispatch', $record))
                ->openUrlInNewTab()
                ->color('gray'),
            ])
            ->bulkActions([
                /*Tables\Actions\BulkActionGroup::make([

                ]),*/
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    protected function getValidationMessages(): array
    {
        return [
            'items.*.product_id.required' => __('filament-form::validation.required'),
            'items.*.product_id.distinct' => __('filament-form::validation.items.product_id.duplicate'),
            'items.*.quantity.min' => __('filament-form::validation.items.quantity.min', ['min' => 1]),
        ];
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
