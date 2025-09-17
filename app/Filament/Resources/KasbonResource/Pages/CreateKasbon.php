<?php

namespace App\Filament\Resources\KasbonResource\Pages;

use App\Filament\Resources\KasbonResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;


class CreateKasbon extends CreateRecord
{
    protected static string $resource = KasbonResource::class;
    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Pengajuan Kasbon Berhasil')
            ->body('Kasbon Anda sedang menunggu persetujuan.')
            ->success()
            ->send();
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
