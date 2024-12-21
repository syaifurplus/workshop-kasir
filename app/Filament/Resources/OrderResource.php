<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Repeater::make('products')
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Buat Order')
                        ->schema([
                            self::getOrderRepeater(),
                        ])
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Utama')
                        ->schema([
                            Forms\Components\Select::make('user_id')
                                ->label('Pilih Pelanggan')
                                ->relationship('user', 'name')
                                ->required(),
                            Forms\Components\Select::make('payment_id')
                                ->relationship('payment', 'name')
                                ->required(),
                            Forms\Components\TextInput::make('total')
                                ->required()
                                ->numeric()
                                ->default(0),
                            Forms\Components\Textarea::make('note')
                                ->columnSpanFull(),
                        ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getOrderRepeater(): Repeater
    {
        return Repeater::make('orderProducts')
            ->columns([
                'md' => 12,
            ])
            ->schema([
                 Forms\Components\Select::make('product_id')
                    ->label('Produk')
                    ->required()
                    ->options(Product::query()->where('stock', '>', 1)->pluck('name', 'id'))
                    ->columnSpan([
                        'md' => 5,
                    ]),
                 Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->columnSpan([
                        'md' => 2,
                    ]),
                 Forms\Components\TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->columnSpan([
                        'md' => 2,
                    ]),
                Forms\Components\TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->columnSpan([
                        'md' => 3,
                    ]),
                ]);
    }

    public static function updateTotalPrice(Forms\get $get, Forms\set $set): void
    {
        $selectedProducts = collect($get('orderProducts'))->filter(fn($item) => !empty($item['product_id']) && !empty($item['quantity']));

        $prices = $selectedProducts->map(fn($item) => $item['unit_price'] * $item['quantity']);
        $total = $selectedProducts->reduce(function ($total, $product) use ($prices) {
            return $total + $prices->sum();
        }, 0);

        $set('total', $total);
    }
}
