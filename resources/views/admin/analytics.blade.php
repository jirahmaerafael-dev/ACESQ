@extends('layouts.app')

@section('title', 'Queue history & analytics')
@section('page-title', 'Queue history & analytics')

@section('content')
    <section class="mb-7">
        <p class="text-xs font-bold uppercase tracking-widest text-indigo-700">Queue insights</p>
        <h2 class="mt-1 text-2xl font-bold tracking-tight">Queue history &amp; analytics</h2>
        <p class="mt-1 text-sm text-slate-600">Review past tickets, compare service-window volume, and find the busiest window.</p>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <form method="GET" action="{{ route('admin.analytics.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto_auto] lg:items-end">
            <label class="text-sm font-semibold">
                Queue date
                <input type="date" name="date" value="{{ $selectedDate }}" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            </label>
            <label class="text-sm font-semibold">
                Service window
                <select name="window_id" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
                    <option value="">All windows</option>
                    @foreach ($filterWindows as $window)
                        <option value="{{ $window->window_id }}" @selected((string) $selectedWindowId === (string) $window->window_id)>{{ $window->window_name }}</option>
                    @endforeach
                </select>
            </label>
            <button class="rounded-xl bg-blue-700 px-5 py-2.5 font-semibold text-white transition hover:bg-blue-800">Apply filters</button>
            <a href="{{ route('admin.analytics.index') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
        </form>
    </section>

    <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Tickets in selection</p>
            <p class="mt-2 text-3xl font-bold">{{ $ticketCount }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide text-amber-700">Waiting</p>
            <p class="mt-2 text-3xl font-bold">{{ $waitingCount }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide text-blue-700">Called / present</p>
            <p class="mt-2 text-3xl font-bold">{{ $servingCount }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Completed / forfeited</p>
            <p class="mt-2 text-3xl font-bold">{{ $closedCount }}</p>
        </article>
    </section>

    <section class="mt-7 grid gap-5 xl:grid-cols-[1fr_18rem]">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-700">Traffic by service</p>
                    <h3 class="mt-1 text-lg font-bold">Tickets per window</h3>
                </div>
                <p class="text-xs text-slate-500">All windows · {{ \Illuminate\Support\Carbon::parse($selectedDate)->format('M j, Y') }}</p>
            </div>
            <div class="mt-5 space-y-4">
                @forelse ($windowStats as $windowStat)
                    <div class="grid grid-cols-[minmax(6rem,1fr)_2fr_auto] items-center gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold">{{ $windowStat['window_name'] }}</p>
                            <p class="text-xs text-slate-500">{{ $windowStat['queue_prefix'] }}</p>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-slate-100" role="progressbar"
                             aria-label="{{ $windowStat['window_name'] }} ticket volume"
                             aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $windowStat['percentage'] }}">
                            <div class="h-full rounded-full bg-gradient-to-r from-blue-600 to-indigo-500 transition-all"
                                 style="width: {{ $windowStat['percentage'] }}%"></div>
                        </div>
                        <span class="w-8 text-right text-sm font-bold tabular-nums">{{ $windowStat['ticket_count'] }}</span>
                    </div>
                @empty
                    <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-500">No service windows are configured.</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-2xl bg-gradient-to-br from-slate-950 to-indigo-950 p-5 text-white shadow-sm sm:p-6">
            <p class="text-xs font-bold uppercase tracking-widest text-indigo-200">Busiest window</p>
            @if ($busiestWindow)
                <h3 class="mt-4 text-2xl font-bold">{{ $busiestWindow['window_name'] }}</h3>
                <p class="mt-1 text-sm text-indigo-100">Prefix {{ $busiestWindow['queue_prefix'] }}</p>
                <p class="mt-7 text-4xl font-black">{{ $busiestWindow['ticket_count'] }}</p>
                <p class="mt-1 text-sm text-indigo-100">ticket(s) on {{ \Illuminate\Support\Carbon::parse($selectedDate)->format('M j, Y') }}</p>
            @else
                <p class="mt-4 text-lg font-bold">No tickets yet</p>
                <p class="mt-1 text-sm text-indigo-100">There is no queue activity for this date.</p>
            @endif
        </article>
    </section>

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Searchable history</p>
            <h3 class="mt-1 text-xl font-bold">Queue tickets</h3>
            <p class="mt-1 text-sm text-slate-500">Showing tickets for {{ \Illuminate\Support\Carbon::parse($selectedDate)->format('F j, Y') }}{{ $selectedWindowId !== null ? ' · '.$filterWindows->firstWhere('window_id', $selectedWindowId)?->window_name : ' · all service windows' }}.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="p-4">Ticket</th>
                        <th class="p-4">Student</th>
                        <th class="p-4">Window</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Missed calls</th>
                        <th class="p-4">Issued</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($records as $record)
                        <tr>
                            <td class="p-4 font-bold tracking-wide">{{ $record->displayNumber() }}</td>
                            <td class="p-4">{{ $record->student_name ?? '—' }}</td>
                            <td class="p-4">{{ $record->window->window_name }}</td>
                            <td class="p-4 capitalize">{{ $record->status }}</td>
                            <td class="p-4">{{ $record->missed_count }} / 3</td>
                            <td class="p-4 text-slate-500">{{ $record->created_at?->format('g:i A') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-slate-500">No tickets match this date and service window.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($records->hasPages())
            <div class="border-t border-slate-200 p-4">
                {{ $records->links() }}
            </div>
        @endif
    </section>
@endsection
