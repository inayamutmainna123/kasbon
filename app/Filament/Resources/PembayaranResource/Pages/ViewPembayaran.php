<?php

namespace App\Filament\Resources\PembayaranResource\Pages;

use App\Filament\Resources\PembayaranResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;

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
                Infolists\Components\Section::make('Detail Pembayaran')
                    ->schema([
                        Infolists\Components\TextEntry::make('kasbon.karyawan.user.name')
                            ->label('Nama Karyawan'),

                        Infolists\Components\TextEntry::make('kasbon.jumlah')
                            ->label('Total Kasbon')
                            ->money('idr'),

                        Infolists\Components\TextEntry::make('jumlah_bayar')
                            ->label('Jumlah Bayar')
                            ->money('idr'),

                        Infolists\Components\TextEntry::make('sisa')
                            ->label('Sisa Kasbon')
                            ->state(
                                fn($record) =>
                                max(
                                    0,
                                    intval($record->kasbon->jumlah ?? 0) - intval($record->jumlah_bayar ?? 0)
                                )
                            )
                            ->money('idr'),

                        Infolists\Components\TextEntry::make('metode')
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

                        Infolists\Components\TextEntry::make('tanggal_bayar')
                            ->label('Tanggal Bayar')
                            ->date('d M Y'),
                    ])
                    ->columns(2),
            ]);
    }
}
