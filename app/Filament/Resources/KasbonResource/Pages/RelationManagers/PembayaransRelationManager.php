<?php

namespace App\Filament\Resources\KasbonResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class PembayaransRelationManager extends RelationManager
{
    protected static string $relationship = 'pembayarans';
    protected static ?string $title = 'Pembayaran Kasbon';

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('jumlah_bayar')->numeric()->prefix('Rp')->required(),
            Forms\Components\Select::make('metode')
                ->options([
                    'potong_gaji' => 'Potong Gaji',
                    'manual' => 'Manual',
                ])->required(),
            Forms\Components\DatePicker::make('tanggal_bayar')->default(now())->required(),
        ]);
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('jumlah_bayar')->money('idr', true),
            Tables\Columns\TextColumn::make('metode')->badge(),
            Tables\Columns\TextColumn::make('tanggal_bayar')->date(),
        ])->headerActions([
            Tables\Actions\CreateAction::make(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }
}
