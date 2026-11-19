<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    // Langkah 2: Mengubah Icon menjadi User Group
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    // Langkah 3: Mengatur Grouping Menu Sidebar
    protected static ?string $navigationGroup = 'Ratings & Customers';

    // Langkah 3: Mengatur Urutan Menu Nomor 7
    public static function getNavigationSort(): ?int
    {
        return 7;
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
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tombol edit dihapus sesuai gambar tutorial
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

    // Langkah 4: Mengubah rute halaman agar hanya menampilkan daftar (index)
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
        ];
    }
}
