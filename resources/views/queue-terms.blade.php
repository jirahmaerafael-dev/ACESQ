<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Queue terms | ACESQ</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-100 px-4 py-8 text-slate-900 antialiased sm:py-12">
    <main class="mx-auto max-w-3xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">
        <header class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-blue-950 to-blue-800 px-6 py-7 text-white sm:px-9 sm:py-9">
            <div class="absolute -right-10 -top-20 h-56 w-56 rounded-full border-[24px] border-white/5"></div>
            <div class="relative">
                <a href="{{ route('public.windows') }}" class="text-sm font-medium text-blue-100 transition hover:text-white">← Choose another window</a>
                <p class="mt-7 text-xs font-bold uppercase tracking-[0.2em] text-blue-200">Step 2 · Confirm your details</p>
                <div class="mt-2 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">{{ $window->window_name }}</h1>
                        <p class="mt-2 text-sm text-blue-100">Your ticket will start with <span class="font-bold text-white">{{ $window->queue_prefix }}</span>.</p>
                    </div>
                    <span class="rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-bold text-white">Online queue</span>
                </div>
            </div>
        </header>

        <form method="POST" action="{{ route('public.queue.issue', $window) }}" class="space-y-7 p-6 sm:p-9">
            @csrf
            <div>
                <h2 class="text-xl font-bold">Enter your name to join the queue</h2>
                <p class="mt-1 text-sm text-slate-600">Use the name staff can call when your ticket is ready.</p>
                <label for="student-name" class="mt-4 block text-sm font-semibold">Student name</label>
                <input id="student-name" name="student_name" value="{{ old('student_name') }}" required maxlength="255" autocomplete="name"
                       placeholder="e.g. Juan Dela Cruz"
                       class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-base shadow-sm outline-none transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                @error('student_name')
                    <p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <h2 class="text-lg font-bold">Before you join</h2>
                <ol class="mt-3 list-decimal space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-5 pl-10 text-sm leading-6 text-slate-700">
                <li>Keep your queue page open to monitor your position and the number being served.</li>
                <li>When called, you have a five-minute grace period to arrive and be marked present.</li>
                <li>Your first missed call moves your number back five places; the second moves it back ten places.</li>
                <li>After a third missed call, your number is forfeited and you must get a new number.</li>
                <li>Only one active queue is allowed per student. Queue numbers reset each day.</li>
                </ol>
            </div>

            @if ($errors->has('terms_agreed'))
                <p role="alert" class="rounded-lg bg-red-50 p-3 text-sm text-red-700">
                    Please agree to the terms before receiving a queue number.
                </p>
            @endif

            <label class="flex items-start gap-3 rounded-xl bg-blue-50 p-4 text-sm leading-6 text-blue-950">
                <input type="checkbox" name="terms_agreed" value="1" required class="mt-1 h-4 w-4 accent-blue-700">
                <span>I have read and agree to the queue terms and conditions.</span>
            </label>

            <button type="submit" class="w-full rounded-xl bg-blue-700 px-5 py-3.5 font-bold text-white shadow-lg shadow-blue-700/20 transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">
                Agree and get my ticket
            </button>
        </form>
    </main>
</body>
</html>
