<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CartResource\Pages;
use App\Filament\Resources\CartResource\RelationManagers;
use App\Models\Cart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    // Langkah 2: Mengubah Icon menjadi Shopping Cart
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    // Langkah 3: Mengatur Grouping Menu Sidebar
    protected static ?string $navigationGroup = 'Carts & Orders';

    // Langkah 3: Mengatur Urutan Menu Nomor 4
    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    // Langkah 4: Menonaktifkan fitur Create Data
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    // Mengubah fungsi table sesuai dengan gambar tutorial terbaru Anda
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.title')->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->searchable(),
                Tables\Columns\TextColumn::make('qty'),
                Tables\Columns\TextColumn::make('total')
                    ->money('IDR', locale: 'id')
                    ->getStateUsing(fn ($record) => $record->qty * $record->product->price),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tombol edit dihapus/dinonaktifkan sesuai gambar tutorial
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
            'index' => Pages\ListCarts::route('/'),
        ];
    }
}
