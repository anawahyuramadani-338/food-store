<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Placeholder::make('invoice')
                                    ->label('Invoice')
                                    ->content(fn ($record) => $record->invoice),

                                Placeholder::make('status')
                                    ->label('Status')
                                    ->content(fn ($record) => view('filament.components.status-badge', ['status' => $record->status])),
                            ]),

                        Placeholder::make('General Information')
                            ->label('General Information')
                            ->content(''),

                        Grid::make(2)
                            ->schema([
                                Placeholder::make('customer_name')
                                    ->label('Customer Name')
                                    ->content(fn ($record) => $record->customer->name),

                                Placeholder::make('customer_email')
                                    ->label('Customer Email')
                                    ->content(fn ($record) => $record->customer->email),
                            ]),

                        Placeholder::make('Shipping Info')
                            ->label('Shipping Info')
                            ->content(''),

                        Grid::make(2)
                            ->schema([
                                Placeholder::make('shipping_courier')
                                    ->label('Courier')
                                    ->content(fn ($record) => $record->shipping->courier ?? '-'),

                                Placeholder::make('shipping_cost')
                                    ->label('Shipping Cost')
                                    ->content(fn ($record) => isset($record->shipping->cost) ? 'IDR ' . number_format($record->shipping->cost, 0, ',', '.') : '-'),
                            ]),

                        Placeholder::make('Transaction Details')
                            ->label('Transaction Details')
                            ->content(''),

                        Repeater::make('transactionDetails')
                            ->relationship('transactionDetails')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Placeholder::make('product_title')
                                            ->label('Product')
                                            ->content(fn ($record) => $record->product->title ?? '-'),

                                        Placeholder::make('qty')
                                            ->label('Quantity')
                                            ->content(fn ($record) => $record->qty),

                                        Placeholder::make('price')
                                            ->label('Price')
                                            ->content(fn ($record) => isset($record->price) ? 'IDR ' . number_format($record->price, 0, ',', '.') : '-'),
                                    ]),
                            ])
                            ->disableItemCreation()
                            ->disableItemDeletion()
                            ->disableItemMovement(),
                    ]),
            ]);
    }
}
