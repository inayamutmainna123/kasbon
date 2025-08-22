<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard Kasbon';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\DashboardPembayaranStats::class,
            \App\Filament\Widgets\MetodePembayaranChart::class,
            \App\Filament\Widgets\PembayaranBulananChart::class,
        ];
    }
}
