<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Actions;

use App\Models\Event;
use App\Services\QrCodeGenerator;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

/**
 * "Show QR" – previews the code for the public event page and offers
 * PNG / SVG downloads.
 */
class ShowQrCodeAction
{
    public static function make(): Action
    {
        return Action::make('showQr')
            ->label(__('admin.qr.action'))
            ->icon(Heroicon::OutlinedQrCode)
            ->color('gray')
            ->modalHeading(fn (Event $record): string => __('admin.qr.heading', ['name' => $record->name]))
            ->modalWidth('md')
            ->modalContent(fn (Event $record) => view('filament.event-qr', [
                'event' => $record,
                'url' => $record->publicUrl(),
                'svg' => app(QrCodeGenerator::class)->svg($record->publicUrl()),
            ]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('admin.qr.close'));
    }
}
