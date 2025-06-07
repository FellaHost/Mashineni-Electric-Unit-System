<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Filament\Resources\TransactionResource;
use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
       return TransactionResource\Pages\ListTransactions::class;
       
 
    } 
    protected function getStats(): array
    {
        return [
            //
            Stat::make('Jumla Ankara', $this->getPageTableQuery()->count()),
            Stat::make('Jumla Matumizi','TZS'.number_format($this->getPageTableQuery()->sum('amount'),2)),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }
}
