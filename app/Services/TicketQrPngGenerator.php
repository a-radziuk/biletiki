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

    public function png(string $payload): string
    {
        $renderer = new GDLibRenderer(self::PIXEL_SIZE, self::MARGIN);
        $writer = new Writer($renderer);

        return $writer->writeString($payload);
    }
}
