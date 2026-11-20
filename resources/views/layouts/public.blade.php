<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choose a queue | ACESQ</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <header class="bg-slate-950 text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8">
            <div class="flex items-center gap-3">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-500 text-sm font-black tracking-wide">AQ</span>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">ACESQ</h1>
                    <p class="text-sm text-slate-300">ACES Tagum College · Registrar’s Office</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="rounded-xl border border-slate-700 px-4 py-2.5 text-sm font-semibold transition hover:border-slate-500 hover:bg-slate-800">
                Staff sign in
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-5 py-8 sm:px-8 sm:py-12">
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-blue-950 to-blue-800 px-6 py-9 text-white shadow-xl shadow-blue-950/15 sm:px-10 sm:py-12">
            <div class="absolute -right-16 -top-24 h-72 w-72 rounded-full border-[32px] border-white/5"></div>
            <div class="relative grid gap-8 lg:grid-cols-[1fr_auto] lg:items-end">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-blue-200">Campus service queue</p>
                    <h2 class="mt-3 text-3xl font-bold leading-tight tracking-tight sm:text-5xl">Your time matters.<br>Take a number and keep moving.</h2>
                    <p class="mt-4 max-w-xl text-sm leading-6 text-blue-100 sm:text-base">Join the line for the service you need. Your ticket updates live while you wait.</p>
                </div>
                <div class="grid grid-cols-2 gap-3 rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                    <div class="rounded-xl bg-white/10 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-100">Open windows</p>
                        <p class="mt-2 text-3xl font-bold">{{ $windows->count() }}</p>
                    </div>
                    <div class="rounded-xl bg-white/10 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-100">Ticket refresh</p>
                        <p class="mt-2 text-lg font-bold">Live</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-10">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-700">Step 1 · Select service</p>
                    <h2 class="mt-1 text-2xl font-bold tracking-tight">Choose a service window</h2>
                </div>
                <p class="text-sm text-slate-500">Have your student name ready to join the queue.</p>
            </div>

            <section class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($windows as $window)
                    <a href="{{ route('public.queue.terms', $window) }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-blue-300 hover:shadow-lg">
                        <div class="flex items-start justify-between gap-4">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-50 text-lg font-bold text-blue-800 transition group-hover:bg-blue-700 group-hover:text-white">{{ $loop->iteration }}</span>
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">OPEN</span>
                        </div>
                        <h3 class="mt-5 text-xl font-bold">{{ $window->window_name }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Get help at this service counter and follow your position from your phone.</p>
                        <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                            <span class="text-sm font-semibold text-slate-500">Tickets start with <span class="font-bold text-slate-800">{{ $window->queue_prefix }}</span></span>
                            <span class="font-semibold text-blue-700 transition group-hover:translate-x-1">Join queue →</span>
                        </div>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-slate-600 sm:col-span-2 xl:col-span-3">
                        <p class="font-bold text-slate-800">No service windows are open right now.</p>
                        <p class="mt-1 text-sm">Please check again later or contact the Registrar’s Office.</p>
                    </div>
                @endforelse
            </section>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white px-5 py-6 text-center text-sm text-slate-500">
        &copy; {{ date('Y') }} ACESQ · Campus queue management · Queue numbers reset daily.
    </footer>
</body>
</html>
