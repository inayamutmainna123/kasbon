<?php

namespace App\Filament\Resources\KaryawanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class KasbonsRelationManager extends RelationManager
{
    // WAJIB sama dengan nama method relasi di model Karyawan
    protected static string $relationship = 'kasbons';

    protected static ?string $title = 'Kasbon Karyawan';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('jumlah')
                ->label('Jumlah Kasbon')
                ->numeric()
                ->minValue(0)
                ->prefix('Rp')
                ->required(),

            Forms\Components\Textarea::make('alasan')
                ->label('Alasan')
                ->rows(3)
                ->nullable(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'lunas'    => 'Lunas',
                ])
                ->default('pending')
                ->required(),

            Forms\Components\DatePicker::make('tanggal_pengajuan')
                ->label('Tanggal Pengajuan')
                ->default(now())
                ->required(),

            Forms\Components\DatePicker::make('tanggal_approval')
                ->label('Tanggal Approval')
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('idr', true)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                        'primary' => 'lunas',
                    ])
                    ->icons([
                        'heroicon-o-clock'  => 'pending',
                        'heroicon-o-check'  => 'approved',
                        'heroicon-o-x-mark' => 'rejected',
                        'heroicon-o-banknotes'   => 'lunas',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_pengajuan')
                    ->label('Pengajuan')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_approval')
                    ->label('Approval')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(), // tambah kasbon dari halaman karyawan
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->color('primary'),
                Tables\Actions\DeleteAction::make()->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
