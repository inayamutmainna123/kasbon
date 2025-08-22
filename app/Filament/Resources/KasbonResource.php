<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KasbonResource\Pages;
use App\Models\Kasbon;
use Doctrine\DBAL\Schema\Table;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class KasbonResource extends Resource
{
    protected static ?string $model = Kasbon::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $pluralLabel = 'Kasbon';
    protected static ?string $slug = 'kasbon';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Kasbon';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Card::make()->columns(2)->schema([
                Select::make('karyawan_id')
                    ->label('Karyawan')
                    ->relationship('karyawan', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->user->name ?? '-')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('jumlah')
                    ->label('Jumlah Kasbon')
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
                        $digits = preg_replace('/\D/', '', (string) $state); // buang selain angka
                        $component->state($digits === '' ? null : number_format((int) $digits, 0, ',', '.'));
                    })

                    ->dehydrateStateUsing(
                        fn($state) =>
                        $state === null ? null : (int) str_replace('.', '', $state)
                    )
                    ->helperText('Masukkan jumlah kasbon. Format ribuan otomatis, data tersimpan sebagai angka murni.')
                    ->columnSpanFull(),


                DatePicker::make('tanggal_pengajuan')
                    ->label('Tanggal Pengajuan')
                    ->default(now())
                    ->native()
                    ->required(),

                DatePicker::make('tanggal_approval')
                    ->label('Tanggal Approval'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'lunas'    => 'Lunas',
                    ])
                    ->default('pending')
                    ->required(),

                Textarea::make('alasan')
                    ->label('Alasan Kasbon')
                    ->rows(3)
                    ->columnSpanFull()
                    ->required(),
            ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([


                TextColumn::make('karyawan.user.name')
                    ->label('Nama Karyawan')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('jumlah')
                    ->label('Jumlah Kasbon')
                    ->money('idr', true)
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                        'primary' => 'lunas',
                    ])
                    ->icons([
                        'heroicon-o-clock'   => 'pending',
                        'heroicon-o-check'   => 'approved',
                        'heroicon-o-x-mark'  => 'rejected',
                        'heroicon-o-banknotes'    => 'lunas',
                    ]),

                TextColumn::make('tanggal_pengajuan')
                    ->label('Tgl Pengajuan')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('tanggal_approval')
                    ->label('Tgl Approval')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'lunas'    => 'Lunas',
                    ]),

                Filter::make('tanggal_pengajuan')
                    ->form([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('tanggal_pengajuan', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('tanggal_pengajuan', '<=', $data['until']));
                    }),
            ])
            ->actions([

                Action::make('markLunas')
                    ->label('')

                    ->icon('heroicon-o-document-check')
                    ->color('primary')
                    ->visible(fn($record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->tooltip('klik untuk melunaskan')
                    ->action(fn($record) => $record->update([
                        'status' => 'lunas',
                    ])),
                Tables\Actions\ActionGroup::make([


                    Tables\Actions\ViewAction::make()->icon('heroicon-o-eye')->color('secondary'),
                    Tables\Actions\EditAction::make()->icon('heroicon-o-pencil')->color('primary'),

                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status === 'pending')
                        ->action(fn($record) => $record->update([
                            'status' => 'approved',
                            'tanggal_approval' => now(),
                        ])),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn($record) => $record->status === 'pending')
                        ->action(fn($record) => $record->update([
                            'status' => 'rejected',
                            'tanggal_approval' => now(),
                        ])),



                    Tables\Actions\DeleteAction::make()->icon('heroicon-o-trash')->color('danger'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKasbons::route('/'),
            'create' => Pages\CreateKasbon::route('/create'),
            'edit'   => Pages\EditKasbon::route('/{record}/edit'),
            'view'   => Pages\ViewKasbon::route('/{record}'),
        ];
    }
}
