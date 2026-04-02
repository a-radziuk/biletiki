@extends('layouts.app')

@section('title', 'Checkout — '.config('app.name'))

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Complete your purchase</h1>
            <p class="mt-1 text-slate-600">{{ $event->name }}</p>
        </div>

        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Your selection</h2>
            <ul class="mt-4 divide-y divide-slate-100">
                @foreach ($lines as $line)
                    <li class="flex justify-between py-3 text-sm">
                        <span>{{ $line['section']->name }} × {{ $line['quantity'] }}</span>
                        <span>{{ number_format($line['section']->price * $line['quantity'], 2) }} {{ strtoupper(config('services.stripe.currency', 'usd')) }}</span>
                    </li>
                @endforeach
            </ul>
            <p class="mt-4 flex justify-between border-t border-slate-200 pt-4 text-base font-semibold">
                <span>Total</span>
                <span>{{ number_format($total, 2) }} {{ strtoupper(config('services.stripe.currency', 'usd')) }}</span>
            </p>
        </section>

        <form action="{{ route('purchase.store') }}" method="post" class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Your details</h2>

            <div>
                <label for="customer_name" class="block text-sm font-medium text-slate-700">Full name</label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required
                    class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('customer_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email <span class="text-red-600">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
                    class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-slate-500">Tickets will be sent here after payment.</p>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-slate-700">Phone <span class="text-slate-400">(optional)</span></label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                    class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @error('payment')
                <p class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800">{{ $message }}</p>
            @enderror

            <button type="submit" class="mt-2 inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-indigo-500">
                Pay with Stripe
            </button>
        </form>
    </div>
@endsection
