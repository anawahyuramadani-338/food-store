<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    // Langkah 2: Mengubah Icon menjadi Shopping Bag
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    // Langkah 4: Mengatur Grouping Menu Sidebar
    protected static ?string $navigationGroup = 'Master Data';

    // Langkah 4: Mengatur Urutan Menu Nomor 2
    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Product Image')
                            ->placeholder('Product Image')
                            ->required()
                            ->directory('products'),

                        Forms\Components\TextInput::make('title')
                            ->label('Product Title')
                            ->placeholder('Product Title')
                            ->required(),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('category_id')
                                ->label('Category')
                                ->relationship('category', 'name')
                                ->required(),

                            Forms\Components\TextInput::make('price')
                                ->label('Price')
                                ->placeholder('Price')
                                ->required(),

                            Forms\Components\TextInput::make('weight')
                                ->label('Weight')
                                ->placeholder('Weight')
                                ->required(),
                        ]),

                        Forms\Components\RichEditor::make('description')
                            ->label('Product Description')
                            ->placeholder('Product Description')
                            ->required(),
                    ]),
            ]);
    }

    // Mengubah fungsi table sesuai dengan gambar tutorial terbaru Anda
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->circular(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('category.name')->searchable(),
                Tables\Columns\TextColumn::make('price')->money('IDR', locale: 'id'),
                Tables\Columns\TextColumn::make('description')->html()->limit(50),
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
