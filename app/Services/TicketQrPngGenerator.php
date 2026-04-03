<?php

namespace App\Services;

use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;

/**
 * Renders ticket QR codes as PNG bytes using PHP GD (Bacon QR v3 GDLibRenderer).
 */
final class TicketQrPngGenerator
{
    private const PIXEL_SIZE = 180;

    private const MARGIN = 1;

    public function png(string $payload, ?int $pixelSize = null): string
    {
        $pixelSize ??= self::PIXEL_SIZE;
        $renderer = new GDLibRenderer($pixelSize, self::MARGIN);
        $writer = new Writer($renderer);

        return $writer->writeString($payload);
    }
}
