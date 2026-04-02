@extends('layouts.app')

@section('title', $event->name.' — '.config('app.name'))

@section('content')
    <article class="space-y-8">
        <header class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-900">{{ $event->name }}</h1>
            <p class="text-slate-600">
                {{ $event->starts_at->timezone(config('app.timezone'))->format('l, F j, Y \a\t g:i A') }}
                · {{ $event->location }}
            </p>
        </header>

        <div class="whitespace-pre-line text-slate-700 leading-relaxed">{{ $event->description }}</div>

        <form action="{{ route('checkout.store', $event) }}" method="post" class="space-y-6">
            @csrf
            <h2 class="text-lg font-semibold text-slate-900">Seating &amp; pricing</h2>
            <p class="text-sm text-slate-600">Choose up to <strong>10 tickets</strong> in total across all sections.</p>

            <ul class="divide-y divide-slate-200 rounded-xl border border-slate-200 bg-white shadow-sm">
                @foreach ($event->sections as $section)
                    <li class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-medium text-slate-900">{{ $section->name }}</p>
                            <p class="text-sm text-slate-600">
                                {{ number_format($section->price, 2) }} {{ strtoupper(config('services.stripe.currency', 'usd')) }} each
                                · {{ $section->available() }} left
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <label for="section-{{ $section->id }}" class="sr-only">Tickets for {{ $section->name }}</label>
                            <input
                                type="number"
                                name="sections[{{ $section->id }}]"
                                id="section-{{ $section->id }}"
                                min="0"
                                max="{{ min(10, $section->available()) }}"
                                value="{{ old('sections.'.$section->id, 0) }}"
                                class="w-24 rounded-lg border-slate-300 text-center shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>
                    </li>
                @endforeach
            </ul>

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-indigo-500 sm:w-auto">
                Continue to checkout
            </button>
        </form>
    </article>
@endsection
