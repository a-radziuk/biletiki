@extends('layouts.app')

@section('title', 'Thank you — '.config('app.name'))

@section('content')
    <div class="space-y-10">
        <div class="rounded-2xl bg-gradient-to-br from-violet-600 via-indigo-600 to-slate-900 px-6 py-10 text-center shadow-xl shadow-indigo-500/20 md:px-10 md:py-12">
            <p class="text-xs font-semibold uppercase tracking-widest text-white/70">Success</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-white md:text-4xl">Thank you!</h1>
            <p class="mt-4 text-lg font-medium text-white/95">
                Your order for <strong class="text-white">{{ $order->event->name }}</strong> is confirmed.
            </p>
            <p class="mt-5 text-base text-white/90">
                @if ($order->tickets->isNotEmpty())
                    A copy of your tickets was also sent to <strong class="text-white">{{ $order->email }}</strong>.
                @else
                    If payment completed successfully, your tickets with QR codes will be sent to <strong class="text-white">{{ $order->email }}</strong>.
                @endif
            </p>
            <p class="mt-4 text-sm font-medium text-white/80">
                Order reference:
                <code class="rounded-md bg-white/15 px-2 py-1 text-xs text-white">{{ $order->uuid }}</code>
            </p>
            <p class="mt-8 text-sm text-white/70">
                Did not receive the email? Check spam, or open the confirmation link from Stripe’s receipt page.
            </p>
        </div>

        @if ($order->tickets->isNotEmpty())
            <div class="rounded-xl bg-gradient-to-b from-white to-slate-50 p-6 ring-1 ring-slate-200/80 md:p-8">
                <h2 class="text-xl font-bold text-slate-900">Your tickets</h2>
                <p class="mt-2 text-base text-slate-600">Show these QR codes at the entrance.</p>

                <ul class="mt-6 space-y-6">
                    @foreach ($order->tickets as $ticket)
                        <li class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200/60 md:p-6">
                            <h3 class="text-lg font-bold text-slate-900">{{ $ticket->section->name }}</h3>
                            @php
                                $png = app(\App\Services\TicketQrPngGenerator::class)->png($ticket->public_code);
                            @endphp
                            <img
                                src="data:image/png;base64,{{ base64_encode($png) }}"
                                alt="Ticket QR"
                                width="180"
                                height="180"
                                class="mx-auto mt-4 block max-w-[180px] rounded-lg bg-white p-2 shadow-md ring-1 ring-slate-200/80"
                            >
                            <p class="mt-4 text-center text-sm font-medium text-slate-600">
                                Code:
                                <code class="rounded-md bg-violet-50 px-2 py-1 text-xs font-mono text-violet-900 ring-1 ring-violet-200/60">{{ $ticket->public_code }}</code>
                            </p>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection
