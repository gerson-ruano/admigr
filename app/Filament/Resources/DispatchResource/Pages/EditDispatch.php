<?php

namespace App\Filament\Resources\DispatchResource\Pages;

use App\Filament\Resources\DispatchResource;
use App\Models\Dispatch;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDispatch extends EditRecord
{
    protected static string $resource = DispatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return route('filament.escritorio.resources.dispatches.index');
    }

}
