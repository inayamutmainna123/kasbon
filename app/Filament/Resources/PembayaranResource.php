<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranResource\Pages;
use App\Models\Pembayaran;
use Doctrine\DBAL\Query\From;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Infolists;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Select;


class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Kasbon';
    protected static ?string $slug = 'pembayaran';
    protected static ?string $navigationLabel = 'Pembayaran Kasbon';
    protected static ?string $pluralModelLabel = 'Pembayaran Kasbon';
    protected static ?string $modelLabel = 'Pembayaran';

    // === FORM ===
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('karyawan_id')
                    ->label('Karyawan')
                    ->relationship('karyawan', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->user?->name ?? '-')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->helperText('Pilih karyawan yang ingin dibayarkan')
                    ->columnSpanFull()
                    ->afterStateUpdated(function (callable $set) {

                        $set('kasbon_id', null);
                    }),

                Forms\Components\Select::make('kasbon_id')
                    ->label('Kasbon')
                    ->options(function (callable $get, callable $set) {
                        $karyawanId = $get('karyawan_id');
                        if (!$karyawanId) return [];

                        $kasbons = \App\Models\Kasbon::where('karyawan_id', $karyawanId)
                            ->where(function ($query) {
                                $query->whereRaw('jumlah > (SELECT COALESCE(SUM(jumlah_bayar),0) FROM pembayaran WHERE pembayaran.kasbon_id = kasbon.id)');
                            })
                            ->pluck('jumlah', 'id');


                        if ($kasbons->isNotEmpty()) {
                            $set('kasbon_id', $kasbons->keys()->first());
                        }

                        return $kasbons->map(
                            fn($jumlah) => 'Rp ' . number_format((int) $jumlah, 0, ',', '.')
                        )->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn($record) => 'Rp ' . number_format((int) $record, 0, ',', '.'))
                    ->required()
                    ->reactive()
                    ->helperText('Kasbon otomatis difilter sesuai karyawan yang dipilih dan belum lunas')
                    ->columnSpanFull(),


                TextInput::make('jumlah_bayar')
                    ->label('Jumlah Bayar')
                    ->prefix('Rp ')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateHydrated(function (TextInput $component, $state) {
                        if ($state === null || $state === '') return;
                        $component->state(number_format((int) $state, 0, ',', '.'));
                    })

                    ->afterStateUpdated(function (TextInput $component, $state) {
                        if ($state === null || $state === '') {
                            $component->state(null);
                            return;
                        }
                        $digits = preg_replace('/\D/', '', (string) $state); // ambil hanya angka
                        $component->state($digits === '' ? null : number_format((int) $digits, 0, ',', '.'));
                    })

                    ->dehydrateStateUsing(
                        fn($state) =>
                        $state === null ? null : (int) str_replace('.', '', $state)
                    )
                    ->helperText('Masukkan jumlah bayar. Otomatis diformat ribuan, tersimpan sebagai angka murni.')
                    ->columnSpanFull(),


                Forms\Components\Select::make('metode')
                    ->label('Metode Pembayaran')
                    ->options([
                        'potong_gaji' => 'Potong Gaji',
                        'manual'      => 'Manual',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->default(now())
                    ->required(),
            ]);
    }

    // === TABEL ===
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('kasbon.karyawan.user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->sortable(),


                Tables\Columns\TextColumn::make('kasbon.jumlah')
                    ->label('Total Kasbon')
                    ->money('idr', true),

                Tables\Columns\TextColumn::make('jumlah_bayar')
                    ->label('Jumlah Bayar')
                    ->money('idr', true)
                    ->sortable(),



                Tables\Columns\TextColumn::make('metode')
                    ->label('Metode')
                    ->badge()
                    ->sortable()
                    ->colors([
                        'primary' => 'potong_gaji',
                        'info'    => 'manual',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'potong_gaji' => 'Potong Gaji',
                        'manual'      => 'Manual',
                        default       => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('tanggal_bayar', 'desc')
            ->filters([
                SelectFilter::make('metode')
                    ->label('Metode Pembayaran')
                    ->options([
                        'potong_gaji' => 'Potong Gaji',
                        'manual'      => 'Manual',
                    ]),
                Filter::make('tanggal_bayar')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('tanggal_bayar', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('tanggal_bayar', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([



                    Tables\Actions\ViewAction::make()
                        ->label('Detail')
                        ->icon('heroicon-o-eye')
                        ->color('secondary')
                        ->url(fn($record) => static::getUrl('view', ['record' => $record])),


                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil')
                        ->color('primary'),

                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->color('danger'),

                ]),


            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Hapus Terpilih'),

            ]);
    }

    // === INFOLIST (DETAIL) ===
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Pembayaran')
                    ->schema([
                        Infolists\Components\TextEntry::make('kasbon.karyawan.user.name')->label('Karyawan'),
                        Infolists\Components\TextEntry::make('kasbon.jumlah')->label('Total Kasbon')->money('idr'),
                        Infolists\Components\TextEntry::make('jumlah_bayar')->label('Jumlah Bayar')->money('idr'),
                        Infolists\Components\TextEntry::make('metode')->label('Metode')->badge(),
                        Infolists\Components\TextEntry::make('tanggal_bayar')->label('Tanggal')->date('d M Y'),

                    ])->columns(2),
            ]);
    }

    // === RELATION MANAGER ===
    public static function getRelations(): array
    {
        return [];
    }

    // === HALAMAN ===
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit'   => Pages\EditPembayaran::route('/{record}/edit'),
            'view'   => Pages\ViewPembayaran::route('/{record}'),
        ];
    }
}
