<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class ViewPembayaran extends ViewRecord
{
    protected static string $resource = PembayaranResource::class;

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail Pembayaran')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('kasbon.karyawan.user.name')
                                    ->label('Nama Karyawan')
                                    ->icon('heroicon-o-user')
                                    ->badge(),

                                TextEntry::make('kasbon.jumlah')
                                    ->label('Total Kasbon')
                                    ->money('idr', true)
                                    ->color('primary')
                                    ->badge(),

                                TextEntry::make('jumlah_bayar')
                                    ->label('Jumlah Bayar')
                                    ->money('idr', true)
                                    ->color('success')
                                    ->badge(),

                                TextEntry::make('sisa')
                                    ->label('Sisa Kasbon')
                                    ->state(
                                        fn($record) =>
                                        max(
                                            0,
                                            intval($record->kasbon->jumlah ?? 0) - intval($record->jumlah_bayar ?? 0)
                                        )
                                    )
                                    ->money('idr', true)
                                    ->color('danger')
                                    ->badge(),

                                TextEntry::make('metode')
                                    ->label('Metode Pembayaran')
                                    ->badge()
                                    ->colors([
                                        'primary' => 'potong_gaji',
                                        'success' => 'manual',
                                    ])
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'potong_gaji' => 'Potong Gaji',
                                        'manual'      => 'Manual',
                                        default       => ucfirst($state),
                                    }),

                                TextEntry::make('tanggal_bayar')
                                    ->label('Tanggal Bayar')
                                    ->date('d M Y')
                                    ->icon('heroicon-o-calendar'),
                            ]),
                    ])
                    ->collapsible()
                    ->icon('heroicon-o-credit-card')
                    ->columns(2),
            ]);
    }
}
