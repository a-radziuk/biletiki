<x-mail::message>
# Your tickets

Thank you for your purchase, **{{ $order->customer_name }}**.

**Event:** {{ $order->event->name }}

**When:** {{ $order->event->starts_at->timezone(config('app.timezone'))->format('l, F j, Y \a\t g:i A') }}

**Where:** {{ $order->event->location }}

Each ticket is attached as a **PDF** (one file per seat). Open the attachment on your phone or print it — each PDF includes the event details, ticket category, QR code, and entry code for that ticket.

@if ($order->tickets->isNotEmpty())
**Your tickets:**
@foreach ($order->tickets as $ticket)
- {{ $ticket->section->name }}
@endforeach
@endif

<x-mail::button :url="route('purchase.thank-you', $order)">
View confirmation
</x-mail::button>

See you at the event.

</x-mail::message>
