@extends('layouts.app')

@section('title', 'Thank you — '.config('app.name'))

@section('content')
    <div class="space-y-8">
        <div class="rounded-xl border border-emerald-200 bg-emerald-50/80 p-8 text-center shadow-sm">
            <h1 class="text-2xl font-semibold text-emerald-900">Thank you!</h1>
            <p class="mt-3 text-emerald-800">
                Your order for <strong>{{ $order->event->name }}</strong> is confirmed.
            </p>
            <p class="mt-4 text-sm text-emerald-800">
                @if ($order->tickets->isNotEmpty())
                    A copy of your tickets was also sent to <strong>{{ $order->email }}</strong>.
                @else
                    If payment completed successfully, your tickets with QR codes will be sent to <strong>{{ $order->email }}</strong>.
                @endif
            </p>
            <p class="mt-2 text-sm text-emerald-700">
                Order reference: <code class="rounded bg-white/60 px-1 py-0.5 text-xs">{{ $order->uuid }}</code>
            </p>
            <p class="mt-6 text-xs text-emerald-700">
                Did not receive the email? Check spam, or open the confirmation link from Stripe’s receipt page.
            </p>
        </div>

        @if ($order->tickets->isNotEmpty())
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Your tickets</h2>
                <p class="mt-1 text-sm text-slate-600">Show these QR codes at the entrance.</p>

                <ul class="mt-6 space-y-8">
                    @foreach ($order->tickets as $ticket)
                        <li class="border-t border-slate-100 pt-8 first:border-t-0 first:pt-0">
                            <h3 class="font-medium text-slate-900">{{ $ticket->section->name }}</h3>
                            @php
                                $png = app(\App\Services\TicketQrPngGenerator::class)->png($ticket->public_code);
                            @endphp
                            <img
                                src="data:image/png;base64,{{ base64_encode($png) }}"
                                alt="Ticket QR"
                                width="180"
                                height="180"
                                class="mx-auto mt-3 block max-w-[180px]"
                            >
                            <p class="mt-3 text-center text-xs text-slate-600">
                                Code: <code class="rounded bg-slate-100 px-1.5 py-0.5">{{ $ticket->public_code }}</code>
                            </p>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection
