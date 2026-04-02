<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-100 via-white to-violet-50/80 text-slate-900 antialiased">
    <main class="mx-auto max-w-3xl px-4 py-8 md:px-6 md:py-12">
        <div class="rounded-2xl bg-white/90 p-6 shadow-lg shadow-slate-900/5 ring-1 ring-slate-200/80 backdrop-blur-sm md:p-8 lg:p-10">
            @if (session('error'))
                <p class="mb-6 rounded-xl bg-red-50 px-4 py-4 text-base font-medium text-red-800 ring-1 ring-red-200/80" role="alert">{{ session('error') }}</p>
            @endif
            @yield('content')
        </div>
    </main>
    <footer class="mx-auto max-w-3xl px-4 pb-8 pt-2 text-center text-sm text-slate-500 md:px-6">
        Crafted by <a href="https://givi.studio/" target="_blank" rel="noopener noreferrer" class="font-medium text-violet-600 underline decoration-violet-300/70 underline-offset-2 transition hover:text-indigo-600 hover:decoration-indigo-400">Givi</a>
    </footer>
</body>
</html>
