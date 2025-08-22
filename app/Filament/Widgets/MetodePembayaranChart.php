<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class MetodePembayaranChart extends ApexChartWidget
{
    protected static ?string $chartId = 'metodePembayaranChart';
    protected static ?string $heading = 'Distribusi Metode Pembayaran';

    protected function getOptions(): array
    {
        $data = Pembayaran::selectRaw("metode, COUNT(*) as total")
            ->groupBy('metode')
            ->pluck('total', 'metode');

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 350,
            ],
            'labels' => ['Potong Gaji', 'Manual'],
            'series' => [
                $data['potong_gaji'] ?? 0,
                $data['manual'] ?? 0,
            ],
            'colors' => ['#3B82F6', '#F59E0B'],
            'legend' => [
                'position' => 'bottom',
            ],
        ];
    }
}
