@extends('layouts.app')

@section('title', 'Checkout — '.config('app.name'))

@section('content')
    <div class="space-y-10">
        <div class="space-y-1">
            <p class="text-xs font-semibold uppercase tracking-widest text-violet-600">Checkout</p>
            <h1 class="bg-gradient-to-r from-slate-900 via-violet-900 to-indigo-800 bg-clip-text text-3xl font-bold text-transparent md:text-4xl">Complete your purchase</h1>
            <p class="text-lg font-medium text-slate-600">{{ $event->name }}</p>
        </div>

        <section class="rounded-xl bg-gradient-to-br from-white to-slate-50/80 p-6 ring-1 ring-slate-200/80 shadow-sm md:p-8">
            <h2 class="text-xs font-bold uppercase tracking-widest text-slate-500">Your selection</h2>
            <ul class="mt-5 divide-y divide-slate-100">
                @foreach ($lines as $line)
                    <li class="flex justify-between gap-4 py-4 text-base md:text-lg">
                        <span class="font-medium text-slate-800">{{ $line['section']->name }} × {{ $line['quantity'] }}</span>
                        <span class="shrink-0 font-semibold text-slate-900">{{ number_format($line['section']->price * $line['quantity'], 2) }} {{ strtoupper(config('services.stripe.currency', 'usd')) }}</span>
                    </li>
                @endforeach
            </ul>
            <p class="mt-5 flex justify-between border-t border-slate-200 pt-5 text-xl font-bold text-slate-900">
                <span>Total</span>
                <span>{{ number_format($total, 2) }} {{ strtoupper(config('services.stripe.currency', 'usd')) }}</span>
            </p>
        </section>

        <form action="{{ route('purchase.store') }}" method="post" class="space-y-5 rounded-xl bg-gradient-to-br from-violet-50/40 to-white p-6 ring-1 ring-violet-200/50 md:p-8">
            @csrf
            <h2 class="text-xs font-bold uppercase tracking-widest text-violet-800/80">Your details</h2>

            @php
                $inputClass = 'mt-2 block w-full rounded-lg border border-slate-300 bg-white px-4 py-3.5 text-base font-medium text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/25';
            @endphp

            <div>
                <label for="customer_name" class="block text-sm font-semibold text-slate-700">Full name</label>
                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required class="{{ $inputClass }}">
                @error('customer_name')
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" class="{{ $inputClass }}">
                <p class="mt-2 text-sm text-slate-600">Tickets will be sent here after payment.</p>
                @error('email')
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-semibold text-slate-700">Phone <span class="font-normal text-slate-500">(optional)</span></label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="{{ $inputClass }}">
                @error('phone')
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @error('payment')
                <p class="rounded-xl bg-red-50 px-4 py-4 text-sm font-medium text-red-800 ring-1 ring-red-200">{{ $message }}</p>
            @enderror

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 px-8 py-3.5 text-base font-semibold text-white shadow-lg shadow-violet-500/25 transition hover:from-violet-500 hover:to-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2">
                Pay with Stripe
            </button>
        </form>
    </div>
@endsection
