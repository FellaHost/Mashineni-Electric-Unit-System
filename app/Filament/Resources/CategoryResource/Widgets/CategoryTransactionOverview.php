<?php

namespace App\Filament\Resources\CategoryResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class CategoryTransactionOverview extends BaseWidget
{
    public ?Model $record = null;
    protected function getStats(): array
    {
        return [
            //
            Stat::make('Total Transactons', $this->record->transactions->count()),
            Stat::make('Total Amount','TZS'. number_format($this->record->transactions->sum('amount'), 2))
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}
