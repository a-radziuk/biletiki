@extends('layouts.app')

@section('title', $event->name.' — '.config('app.name'))

@section('content')
    <article class="space-y-10">
        <header class="space-y-3">
            <p class="text-xs font-semibold uppercase tracking-widest text-violet-600">Event</p>
            <h1 class="bg-gradient-to-r from-slate-900 via-violet-900 to-indigo-800 bg-clip-text text-3xl font-bold tracking-tight text-transparent md:text-4xl">{{ $event->name }}</h1>
            <p class="text-lg font-medium text-slate-600">
                {{ $event->starts_at->timezone(config('app.timezone'))->format('l, F j, Y \a\t g:i A') }}
                <span class="text-slate-400"> · </span>
                {{ $event->location }}
            </p>
        </header>

        <div class="whitespace-pre-line rounded-xl bg-gradient-to-br from-slate-50 to-violet-50/50 px-5 py-5 text-lg leading-relaxed text-slate-700 ring-1 ring-slate-200/60 md:px-6 md:py-6">{{ $event->description }}</div>

        <form action="{{ route('checkout.store', $event) }}" method="post" class="space-y-8">
            @csrf
            <div>
                <h2 class="text-xl font-bold text-slate-900">Seating &amp; pricing</h2>
                <p class="mt-2 text-base text-slate-600">Choose up to <strong class="text-slate-900">10 tickets</strong> in total across all sections.</p>
            </div>

            <ul class="divide-y divide-slate-100 overflow-hidden rounded-xl bg-white ring-1 ring-slate-200/80 shadow-sm">
                @foreach ($event->sections as $section)
                    <li class="flex flex-col gap-4 p-5 transition-colors hover:bg-violet-50/30 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
                        <div>
                            <p class="text-lg font-bold text-slate-900">{{ $section->name }}</p>
                            <p class="mt-1 text-base text-slate-600">
                                {{ number_format($section->price, 2) }} {{ strtoupper(config('services.stripe.currency', 'usd')) }} each
                                <span class="text-slate-400"> · </span>
                                {{ $section->available() }} left
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <label for="section-{{ $section->id }}" class="sr-only">Tickets for {{ $section->name }}</label>
                            <input
                                type="number"
                                name="sections[{{ $section->id }}]"
                                id="section-{{ $section->id }}"
                                min="0"
                                max="{{ min(10, $section->available()) }}"
                                value="{{ old('sections.'.$section->id, 0) }}"
                                class="h-14 w-28 rounded-lg border border-slate-300 bg-white px-2 text-center text-xl font-semibold text-slate-900 shadow-inner shadow-slate-900/5 placeholder:text-slate-400 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/30"
                            >
                        </div>
                    </li>
                @endforeach
            </ul>

            @if ($errors->any())
                <div class="rounded-xl bg-red-50 px-4 py-4 text-base font-medium text-red-800 ring-1 ring-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 px-10 py-3.5 text-base font-semibold text-white shadow-lg shadow-violet-500/25 transition hover:from-violet-500 hover:to-indigo-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 sm:w-auto">
                Continue to checkout
            </button>
        </form>
    </article>
@endsection
