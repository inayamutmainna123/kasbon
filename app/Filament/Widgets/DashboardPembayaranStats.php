<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use App\Models\Kasbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class  DashboardPembayaranStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Kasbon', 'Rp ' . number_format(Kasbon::sum('jumlah'), 0, ',', '.'))
                ->description('Jumlah kasbon yang diajukan')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('warning'),

            Stat::make('Total Dibayar', 'Rp ' . number_format(Pembayaran::sum('jumlah_bayar'), 0, ',', '.'))
                ->description('Semua pembayaran masuk')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Sisa Kasbon', 'Rp ' . number_format(Kasbon::sum('jumlah') - Pembayaran::sum('jumlah_bayar'), 0, ',', '.'))
                ->description('Kasbon belum terbayar')
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),
        ];
    }
}
