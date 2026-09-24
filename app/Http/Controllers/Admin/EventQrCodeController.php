<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\QrCodeGenerator;
use Illuminate\Http\Response;

/**
 * Downloads the QR code that points attendees to the public event page.
 */
class EventQrCodeController extends Controller
{
    public function __invoke(Event $event, string $format, QrCodeGenerator $qrCodes): Response
    {
        $url = $event->publicUrl();

        [$body, $contentType] = $format === 'svg'
            ? [$qrCodes->svg($url), 'image/svg+xml']
            : [$qrCodes->png($url), 'image/png'];

        return response($body, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => sprintf('attachment; filename="agendly-%s-qr.%s"', $event->slug, $format),
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
