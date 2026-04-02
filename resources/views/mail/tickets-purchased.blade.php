<x-mail::message>
# Your tickets

Thank you for your purchase, **{{ $order->customer_name }}**.

**Event:** {{ $order->event->name }}

**When:** {{ $order->event->starts_at->timezone(config('app.timezone'))->format('l, F j, Y \a\t g:i A') }}

**Where:** {{ $order->event->location }}

@foreach ($order->tickets as $ticket)
---

### {{ $ticket->section->name }}

Show this QR at the entrance:

@php
    $png = app(\App\Services\TicketQrPngGenerator::class)->png($ticket->public_code);
@endphp

<img src="data:image/png;base64,{{ base64_encode($png) }}" alt="Ticket QR" style="display:block;margin:12px 0;">

**Ticket code:** `{{ $ticket->public_code }}`

@endforeach

<x-mail::button :url="route('purchase.thank-you', $order)">
View confirmation
</x-mail::button>

See you at the event.

</x-mail::message>
