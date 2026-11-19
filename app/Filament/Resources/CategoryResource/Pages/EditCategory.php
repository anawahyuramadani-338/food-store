<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Memodifikasi data sebelum disimpan ke database
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // LOGIKA PERBAIKAN TOTAL:
        // Kita cek apakah key 'image' ada dalam array data perubahan.
        // Jika statusnya null atau kosong (artinya user tidak upload foto baru), 
        // barulah kita hapus dari antrean update agar DB tidak error NOT NULL.
        if (array_key_exists('image', $data) && ($data['image'] === null || $data['image'] === '')) {
            unset($data['image']);
        }

        return $data;
    }
}
