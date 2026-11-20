<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Your queue | ACESQ</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <header class="bg-slate-950 px-5 py-4 text-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-blue-500 text-sm font-black tracking-wide">AQ</span>
                <div>
                    <h1 class="text-lg font-bold">ACESQ <span class="font-normal text-slate-400">/ Live ticket</span></h1>
                    <p class="text-sm text-slate-300">{{ $record->window->window_name }}</p>
                </div>
            </div>
            <a href="{{ route('public.windows') }}" class="rounded-xl border border-slate-700 px-4 py-2.5 text-sm font-semibold transition hover:bg-slate-800">
                Queue home
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-12">
        @if (session('notice'))
            <p role="status" class="mb-5 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm font-medium text-blue-900">{{ session('notice') }}</p>
        @endif
        @if ($errors->has('confirm_cancel') || $errors->has('queue'))
            <div role="alert" class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                @error('confirm_cancel')
                    <p>{{ $message }}</p>
                @enderror
                @error('queue')
                    <p>{{ $message }}</p>
                @enderror
            </div>
        @endif

        <section class="overflow-hidden rounded-3xl bg-white shadow-xl shadow-slate-900/5">
            <div class="grid lg:grid-cols-[1.2fr_0.8fr]">
                <div class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-blue-950 to-blue-800 p-7 text-white sm:p-10">
                    <div class="absolute -bottom-28 -right-20 h-80 w-80 rounded-full border-[34px] border-white/5"></div>
                    <div class="relative">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-200">Your queue ticket</p>
                        <div class="mt-3 flex flex-wrap items-end justify-between gap-5">
                            <div>
                                <p id="queue-number" class="text-6xl font-black tracking-tight sm:text-8xl">{{ $record->displayNumber() }}</p>
                                <p class="mt-2 text-sm font-medium text-blue-100">Keep this ticket open while you wait.</p>
                            </div>
                            <span class="rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-center backdrop-blur-sm">
                                <span class="block text-xs font-semibold uppercase tracking-wide text-blue-100">Service</span>
                                <span id="window-name" class="mt-1 block font-bold">{{ $record->window->window_name }}</span>
                            </span>
                        </div>
                        <div class="mt-8 border-t border-white/15 pt-5">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-200">Student</p>
                            <p class="mt-1 text-lg font-bold">{{ $record->student_name ?? 'Student' }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col justify-center p-7 sm:p-10">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-700">Live queue status</p>
                    <p id="queue-status" class="mt-2 text-3xl font-bold capitalize">{{ $status['status'] }}</p>
                    <p id="queue-message" role="status" aria-live="polite" class="mt-2 text-sm leading-6 text-slate-600"></p>
                    <div class="mt-7 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Your position</p>
                            <p id="queue-position" class="mt-2 text-2xl font-bold">{{ $status['position'] ? '#'.$status['position'] : '—' }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Now serving</p>
                            <p id="serving-number" class="mt-2 text-2xl font-bold">{{ $status['serving_ticket_number'] ?? '—' }}</p>
                        </div>
                        <div class="col-span-2 rounded-2xl border border-blue-100 bg-blue-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-800">Estimated wait</p>
                            <p id="estimated-wait" class="mt-1 text-xl font-bold text-blue-950">{{ $status['estimated_wait_minutes'] === null ? '—' : $status['estimated_wait_minutes'].' min' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="called-panel" class="mt-5 hidden rounded-2xl border border-amber-300 bg-amber-50 p-5 shadow-sm">
            <p class="text-lg font-bold text-amber-950">Your ticket has been called!</p>
            <p class="mt-1 text-sm text-amber-900">Please proceed to your service window within <span id="countdown" class="font-bold">05:00</span>.</p>
        </section>

        <section id="cancel-ticket-panel"
                 class="mt-5 rounded-2xl border border-red-200 bg-white p-5 shadow-sm {{ $status['active_today'] && in_array($status['status'], ['waiting', 'called'], true) ? '' : 'hidden' }}">
            <h2 class="text-lg font-bold text-slate-900">No longer need this ticket?</h2>
            <p class="mt-1 text-sm text-slate-600">Cancel it so the next students can move up in line.</p>
            <form method="POST" action="{{ route('public.queue.cancel', $record->public_token) }}" class="mt-4">
                @csrf
                <label class="flex items-start gap-3 text-sm text-slate-700">
                    <input type="checkbox" name="confirm_cancel" value="1" required @checked(old('confirm_cancel')) class="mt-1 h-4 w-4 accent-red-700">
                    <span>I confirm that I no longer need this queue ticket.</span>
                </label>
                <button type="submit" class="mt-4 rounded-xl bg-red-700 px-5 py-3 font-bold text-white transition hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-200">
                    Cancel my ticket
                </button>
            </form>
        </section>

        <p class="mt-5 text-center text-xs text-slate-500">
            Your ticket refreshes automatically every 8 seconds. Queue numbers and positions reset daily.
        </p>
    </main>

    <script>
        const statusUrl = @json(route('public.queue.status', $record->public_token));
        const initialStatus = @json($status);

        function renderStatus(data) {
            document.getElementById('queue-number').textContent = data.ticket_number;
            document.getElementById('window-name').textContent = data.window_name;
            document.getElementById('queue-status').textContent = data.status;
            document.getElementById('queue-position').textContent = data.position ? `#${data.position}` : '—';
            document.getElementById('serving-number').textContent = data.serving_ticket_number || '—';
            document.getElementById('estimated-wait').textContent =
                data.estimated_wait_minutes === null ? '—' : `${data.estimated_wait_minutes} min`;

            const calledPanel = document.getElementById('called-panel');
            calledPanel.classList.toggle('hidden', data.status !== 'called');
            if (data.status === 'called' && data.countdown_seconds !== null) {
                const minutes = Math.floor(data.countdown_seconds / 60);
                const seconds = data.countdown_seconds % 60;
                document.getElementById('countdown').textContent =
                    `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }

            const messages = {
                cancelled: 'Your ticket has been cancelled. You can get a new number if you still need service.',
                forfeited: 'This number has been forfeited after three missed calls. Get a new number if you still need service.',
                completed: 'Your transaction is complete.',
                called: 'Please proceed to your selected window.',
                present: 'You are marked present. Please complete your transaction with staff.',
            };
            document.getElementById('queue-message').textContent =
                !data.active_today ? 'This ticket is from a previous day and is no longer active.' :
                (messages[data.status] || `There are ${data.waiting_count} number(s) waiting.`);

            document.getElementById('cancel-ticket-panel').classList.toggle(
                'hidden',
                !data.active_today || !['waiting', 'called'].includes(data.status)
            );
        }

        async function refreshStatus() {
            try {
                const response = await fetch(statusUrl, { headers: { Accept: 'application/json' }, cache: 'no-store' });
                if (!response.ok) {
                    throw new Error('Queue status could not be refreshed.');
                }
                renderStatus(await response.json());
            } catch (error) {
                document.getElementById('queue-message').textContent = error.message;
            }
        }

        renderStatus(initialStatus);
        window.setInterval(refreshStatus, 8000);
    </script>
</body>
</html>
