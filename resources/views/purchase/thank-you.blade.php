@extends('layouts.app')

@section('title', 'Thank you — '.config('app.name'))

@section('content')
    <div class="rounded-xl border border-emerald-200 bg-emerald-50/80 p-8 text-center shadow-sm">
        <h1 class="text-2xl font-semibold text-emerald-900">Thank you!</h1>
        <p class="mt-3 text-emerald-800">
            Your order for <strong>{{ $order->event->name }}</strong> is confirmed.
        </p>
        <p class="mt-4 text-sm text-emerald-800">
            If payment completed successfully, your tickets with QR codes were sent to <strong>{{ $order->email }}</strong>.
        </p>
        <p class="mt-2 text-sm text-emerald-700">
            You can bookmark this page: your order reference is <code class="rounded bg-white/60 px-1 py-0.5 text-xs">{{ $order->uuid }}</code>
        </p>
        <p class="mt-6 text-xs text-emerald-700">
            Did not receive the email? Check spam, or open the confirmation link from Stripe’s receipt page.
        </p>
    </div>
@endsection
