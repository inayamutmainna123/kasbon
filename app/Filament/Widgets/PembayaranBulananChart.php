<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class PembayaranBulananChart extends ApexChartWidget
{
    protected static ?string $chartId = 'pembayaranDashboard';

    protected static ?string $heading = '📊 Dashboard Pembayaran Kasbon';

    public static function getCards(): array
    {
        $total = Pembayaran::sum('jumlah_bayar');
        $avg   = Pembayaran::avg('jumlah_bayar');
        $max   = Pembayaran::max('jumlah_bayar');

        return [
            Card::make('Total Pembayaran', 'Rp ' . number_format($total, 0, ',', '.'))
                ->description('Akumulasi semua pembayaran')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Card::make('Rata-rata Pembayaran', 'Rp ' . number_format($avg, 0, ',', '.'))
                ->description('Nilai rata-rata pembayaran')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('primary'),

            Card::make('Pembayaran Tertinggi', 'Rp ' . number_format($max, 0, ',', '.'))
                ->description('Nominal pembayaran terbesar')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('warning'),
        ];
    }

    protected function getOptions(): array
    {
        $data = Pembayaran::selectRaw("MONTH(tanggal_bayar) as bulan, SUM(jumlah_bayar) as total")
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
                'toolbar' => ['show' => true],
            ],
            'series' => [
                [
                    'name' => 'Total Pembayaran',
                    'data' => array_values($data->toArray()),
                ],
            ],
            'xaxis' => [
                'categories' => array_map(fn($m) => $months[$m - 1], array_keys($data->toArray())),
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => false,
                    'columnWidth' => '50%',
                    'borderRadius' => 5,
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => ['colors' => ['#fff']],
            ],
            'colors' => ['#EF4444'],
            'legend' => [
                'position' => 'top',
            ],
        ];
    }
}
