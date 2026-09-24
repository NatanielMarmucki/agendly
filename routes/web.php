<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\EventQrCodeController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

/*
| Organizer-only helpers that live next to the Filament panel.
*/
Route::middleware(['auth', 'can:view,event'])
    ->prefix('admin/events/{event}')
    ->name('admin.events.')
    ->group(function (): void {
        Route::get('qr.{format}', EventQrCodeController::class)
            ->whereIn('format', ['png', 'svg'])
            ->name('qr');
    });
