<?php

declare(strict_types=1);

use App\Http\Controllers\Public\AnnouncementController;
use App\Http\Controllers\Public\GroupController;
use App\Http\Controllers\Public\InfoController;
use App\Http\Controllers\Public\PlanController;
use App\Http\Controllers\Public\ScheduleController;
use App\Http\Controllers\Public\SessionController;
use App\Http\Controllers\Public\SpeakerController;
use App\Http\Middleware\EnsureEventIsPublished;
use App\Http\Middleware\SharePublicEventProps;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public attendee app
|--------------------------------------------------------------------------
|
| Served through the lean "public" middleware group (see bootstrap/app.php):
| no session, no CSRF token, no cookies unless a language is chosen.
|
*/

Route::inertia('/', 'Welcome')->name('home');

Route::prefix('e/{event:slug}')
    ->name('public.')
    ->middleware([EnsureEventIsPublished::class, SharePublicEventProps::class])
    ->scopeBindings()
    ->group(function (): void {
        Route::get('/', ScheduleController::class)->name('schedule');
        Route::get('sessions/{session}', SessionController::class)->whereNumber('session')->name('sessions.show');
        Route::get('plan', PlanController::class)->name('plan');
        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements');
        Route::get('announcements.json', [AnnouncementController::class, 'feed'])->name('announcements.feed');
        Route::get('speakers', SpeakerController::class)->name('speakers');
        Route::get('groups', GroupController::class)->name('groups');
        Route::get('info', InfoController::class)->name('info');
    });
