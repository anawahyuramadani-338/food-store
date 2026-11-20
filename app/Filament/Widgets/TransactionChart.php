<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TransactionChart extends ChartWidget
{
    protected static ?string $heading = 'Transactions (30 Days)';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Trend::query(Transaction::query()->where('status', 'success'))
            ->between(
                start: now()->subDays(30)->startOfDay(),
                end: now()->endOfDay(),
            )
            ->perDay()
            ->sum('total');

        return [
            'datasets' => [
                [
                    'label' => 'Total Transactions',
                    'data' => $data->map(fn (TrendValue $value) => (float) $value->aggregate)->toArray(),
                    'fill' => true,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}