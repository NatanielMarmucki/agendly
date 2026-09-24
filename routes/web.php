<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\EventQrCodeController;
use Illuminate\Support\Facades\Route;

/*
| Organizer-only helpers that live next to the Filament panel.
| The attendee app is registered separately in routes/public.php.
*/
Route::middleware(['auth', 'can:view,event'])
    ->prefix('admin/events/{event}')
    ->name('admin.events.')
    ->group(function (): void {
        Route::get('qr.{format}', EventQrCodeController::class)
            ->whereIn('format', ['png', 'svg'])
            ->name('qr');
    });
