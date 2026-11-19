<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RatingResource\Pages;
use App\Filament\Resources\RatingResource\RelationManagers;
use App\Models\Rating;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    // Langkah 2: Mengubah Icon menjadi Sparkles
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    // Langkah 3: Mengatur Grouping Menu Sidebar
    protected static ?string $navigationGroup = 'Ratings & Customers';

    protected static ?string $navigationLabel = 'Ratings & Reviews';

    protected static ?string $pluralLabel = 'Ratings & Reviews';

    // Langkah 3: Mengatur Urutan Menu Nomor 6
    public static function getNavigationSort(): ?int
    {
        return 6;
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
                Tables\Columns\TextColumn::make('customer.name')->searchable(),
                Tables\Columns\TextColumn::make('product.title')->searchable(),
                Tables\Columns\TextColumn::make('rating'),
                Tables\Columns\TextColumn::make('review'),
                Tables\Columns\TextColumn::make('created_at'),
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
            'index' => Pages\ListRatings::route('/'),
        ];
    }
}
