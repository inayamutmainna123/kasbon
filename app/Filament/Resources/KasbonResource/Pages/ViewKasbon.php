<?php

namespace App\Filament\Resources\KasbonResource\Pages;

use App\Filament\Resources\KasbonResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;

class ViewKasbon extends ViewRecord
{
    protected static string $resource = KasbonResource::class;

    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([

                Section::make('📑 Detail Kasbon')
                    ->description('Informasi lengkap mengenai pengajuan kasbon karyawan.')
                    ->schema([
                        Grid::make(2)->schema([

                            // Karyawan
                            TextEntry::make('karyawan.user.name')
                                ->label('Nama Karyawan')
                                ->icon('heroicon-o-user')
                                ->badge()
                                ->color('primary'),

                            // Jumlah kasbon
                            TextEntry::make('jumlah')
                                ->label('Jumlah Kasbon')
                                ->money('idr', true)
                                ->color('success')
                                ->badge()
                                ->icon('heroicon-o-banknotes'),

                            // Status
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->icon('heroicon-o-flag')
                                ->colors([
                                    'warning' => 'pending',
                                    'success' => 'approved',
                                    'danger'  => 'rejected',
                                    'primary' => 'lunas',
                                ]),

                            // Tanggal pengajuan
                            TextEntry::make('tanggal_pengajuan')
                                ->label('Tanggal Pengajuan')
                                ->date('d M Y')
                                ->icon('heroicon-o-calendar'),

                            // Tanggal approval
                            TextEntry::make('tanggal_approval')
                                ->label('Tanggal Approval')
                                ->date('d M Y')
                                ->placeholder('-')
                                ->icon('heroicon-o-check-circle'),
                        ]),

                        // Alasan kasbon
                        Section::make('📝 Alasan Pengajuan')
                            ->schema([
                                TextEntry::make('alasan')
                                    ->label(false)
                                    ->columnSpanFull()
                                    ->placeholder('-'),
                            ])
                            ->collapsible(),
                    ])
                    ->collapsible(),
            ]);
    }
}
