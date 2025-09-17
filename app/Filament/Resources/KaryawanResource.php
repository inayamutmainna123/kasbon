<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KaryawanResource\Pages;

use App\Models\Karyawan;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Karyawan';
    protected static ?string $label = 'Data Karyawan';
    protected static ?string $slug = 'karyawan';
    protected static ?string $pluralLabel = 'Data Karyawan';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Section::make('Informasi Karyawan')
                ->description('Isi data lengkap karyawan berikut')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('user_id')
                            ->label('Nama User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                            ->helperText('Pilih user yang terhubung dengan karyawan ini'),

                        TextInput::make('jabatan')
                            ->label('Jabatan')
                            ->placeholder('Contoh: Staff, Manager, Admin')
                            ->maxLength(100)
                            ->required(),
                    ]),


                    TextInput::make('gaji_pokok')
                        ->label('Gaji Pokok')
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
                            $digits = preg_replace('/\D/', '', (string) $state);  // sisakan angka saja
                            $component->state($digits === '' ? null : number_format((int) $digits, 0, ',', '.'));
                        })

                        ->dehydrateStateUsing(
                            fn($state) =>
                            $state === null ? null : (int) str_replace('.', '', $state)
                        )
                        ->helperText('Ketik angka saja; format ribuan otomatis. Yang disimpan tetap angka murni (tanpa titik).')

                ])
                ->collapsible(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([

            TextColumn::make('index')
                ->label('No')
                ->alignCenter()
                ->rowIndex(),

            TextColumn::make('user.name')
                ->label('Nama Lengkap')
                ->searchable()
                ->sortable()
                ->icon('heroicon-o-user'),

            BadgeColumn::make('jabatan')
                ->label('Jabatan')
                ->sortable()
                ->colors([
                    'primary' => 'Staff',
                    'success' => 'Manager',
                    'warning' => 'Admin',
                    'danger'  => 'Direktur',
                ])
                ->searchable(),

            TextColumn::make('gaji_pokok')
                ->label('Gaji Pokok')
                ->money('idr', true)
                ->sortable(),

            BadgeColumn::make('kasbons_count')
                ->counts('kasbons')
                ->label('Jumlah Kasbon')
                ->colors([
                    'success' => fn($state): bool => $state == 0,
                    'warning' => fn($state): bool => $state > 0 && $state < 3,
                    'danger'  => fn($state): bool => $state >= 3,
                ])
                ->sortable(),
        ])
            ->filters([
                Filter::make('gaji_tinggi')
                    ->label('Gaji di atas 5 juta')
                    ->query(fn($query) => $query->where('gaji_pokok', '>', 5000000)),

                SelectFilter::make('jabatan')
                    ->label('Filter Jabatan')
                    ->options([
                        'Staff'    => 'Staff',
                        'Manager'  => 'Manager',
                        'Admin'    => 'Admin',
                        'Direktur' => 'Direktur',
                    ]),
            ])
            ->actions([

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color('success'),
                    Tables\Actions\EditAction::make()->color('primary'),
                    Tables\Actions\DeleteAction::make()->color('danger'),
                ])

            ])







            ->defaultSort('user.name', 'asc')

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('user.name', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\KaryawanResource\RelationManagers\KasbonsRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'edit' => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }
}
