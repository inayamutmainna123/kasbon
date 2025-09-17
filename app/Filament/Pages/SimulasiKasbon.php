<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class SimulasiKasbon extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Simulasi Kasbon';
    protected static string $view = 'filament.pages.simulasi-kasbon';

    public $jumlah_pinjam;
    public $tenor;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('jumlah_pinjam')
                    ->label('Jumlah Pinjaman')
                    ->numeric()
                    ->required()

                    ->helperText('Masukkan jumlah pinjaman yang diinginkan')
                    ->columnSpanFull()
                    ->live(onBlur: true)
                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                        $component->state($state ?? 0);
                    })
                    ->placeholder('0')
                    ->default(0)
                    ->minValue(0)
                    ->step(1000)

                    ->prefix('Rp')
                    ->reactive(), // penting supaya placeholder update

                Forms\Components\Select::make('tenor')
                    ->label('Tenor (bulan)')
                    ->helperText('Pilih tenor pinjaman')
                    ->columnSpanFull()
                    ->options([
                        1 => '1 Bulan',
                        2 => '2 Bulan',
                        3 => '3 Bulan',
                        6 => '6 Bulan',
                        12 => '12 Bulan',
                    ])
                    ->required()
                    ->reactive(), // penting juga

                Forms\Components\Placeholder::make('hasil')
                    ->label('Hasil Simulasi')
                    ->columnSpanFull()
                    ->reactive()

                    ->content(function (Get $get) {
                        $jumlah = $get('jumlah_pinjam');
                        $tenor  = $get('tenor');

                        if ($jumlah && $tenor) {
                            $cicilan = $jumlah / $tenor;
                            return "Total Pinjaman: Rp " . number_format($jumlah, 0, ',', '.') .
                                " | Cicilan per bulan: Rp " . number_format($cicilan, 0, ',', '.');
                        }

                        return 'Isi jumlah pinjaman & tenor untuk melihat simulasi.';
                    }),
            ]);
    }
}
