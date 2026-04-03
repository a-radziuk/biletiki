<?php

namespace App\Services;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

final class TicketPdfGenerator
{
    private const QR_PIXEL_SIZE = 220;

    public function __construct(
        private TicketQrPngGenerator $qrPng,
    ) {}

    public function generate(Ticket $ticket): string
    {
        $ticket->loadMissing(['section.event', 'order']);

        $png = $this->qrPng->png($ticket->public_code, self::QR_PIXEL_SIZE);
        $qrDataUri = 'data:image/png;base64,'.base64_encode($png);

        $order = $ticket->order;
        $section = $ticket->section;
        $event = $section->event;

        $pdf = Pdf::loadView('pdf.ticket', [
            'ticket' => $ticket,
            'event' => $event,
            'section' => $section,
            'order' => $order,
            'qrDataUri' => $qrDataUri,
            'descriptionPlain' => $this->descriptionPlain($event->description ?? ''),
            'formattedPrice' => Number::currency((float) $section->price, strtoupper($order->currency)),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->output();
    }

    private function descriptionPlain(string $html): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)) ?? '');

        return Str::limit($text, 400);
    }
}
