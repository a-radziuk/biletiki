<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Ticket;
use App\Services\TicketPdfGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketsPurchasedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your tickets — '.$this->order->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.tickets-purchased',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $this->order->loadMissing(['tickets.section.event', 'tickets.order', 'event']);

        $orderUuid = $this->order->uuid;
        $attachments = [];
        foreach ($this->order->tickets as $index => $ticket) {
            $n = $index + 1;
            $filename = sprintf('ticket-%s-%02d.pdf', $orderUuid, $n);
            $ticketId = $ticket->id;

            $attachments[] = Attachment::fromData(
                function () use ($ticketId) {
                    $ticket = Ticket::query()->with(['section.event', 'order'])->findOrFail($ticketId);

                    return app(TicketPdfGenerator::class)->generate($ticket);
                },
                $filename
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
