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
    </div>
@endsection
