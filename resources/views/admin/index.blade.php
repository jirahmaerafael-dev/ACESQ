@extends('layouts.app')

@section('title', 'Admin dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <section class="rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 p-6 text-white shadow-xl shadow-slate-950/10 sm:p-8">
        <div class="flex flex-wrap items-end justify-between gap-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-300">ACES Tagum College · Queue control</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Admin dashboard</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">A snapshot of service activity across the school today.</p>
            </div>
            <span class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-semibold text-blue-100">{{ now()->format('D, M j · g:i A') }}</span>
        </div>

        <div class="mt-7 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-300">Open windows</p>
                <p class="mt-2 text-3xl font-bold">{{ $openWindowCount }} <span class="text-base font-medium text-slate-300">/ {{ $windowCount }}</span></p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-300">Staff accounts</p>
                <p class="mt-2 text-3xl font-bold">{{ $staffCount }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-300">Tickets today</p>
                <p class="mt-2 text-3xl font-bold">{{ $ticketCount }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-300">Waiting / serving</p>
                <p class="mt-2 text-3xl font-bold">{{ $waitingCount }} <span class="text-base font-medium text-slate-300">/ {{ $servingCount }}</span></p>
            </div>
        </div>
    </section>

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Recent activity</p>
                <h3 class="mt-1 text-xl font-bold">Latest tickets today</h3>
            </div>
            <a href="{{ route('admin.queues.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                View all queues
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="p-4">Ticket</th>
                        <th class="p-4">Student</th>
                        <th class="p-4">Window</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Issued</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentRecords as $record)
                        <tr>
                            <td class="p-4 font-bold tracking-wide">{{ $record->displayNumber() }}</td>
                            <td class="p-4">{{ $record->student_name ?? '—' }}</td>
                            <td class="p-4">{{ $record->window->window_name }}</td>
                            <td class="p-4 capitalize">{{ $record->status }}</td>
                            <td class="p-4 text-slate-500">{{ $record->created_at?->format('g:i A') ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-8 text-center text-slate-500">No tickets have been issued today.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <a href="{{ route('admin.windows.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Configuration</p>
            <h3 class="mt-2 font-bold">Manage service windows</h3>
            <p class="mt-1 text-sm text-slate-500">Edit names, ticket prefixes, and availability.</p>
        </a>
        <a href="{{ route('admin.staff.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Access</p>
            <h3 class="mt-2 font-bold">Manage staff accounts</h3>
            <p class="mt-1 text-sm text-slate-500">Create accounts and assign service windows.</p>
        </a>
        <a href="{{ route('admin.queues.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Monitoring</p>
            <h3 class="mt-2 font-bold">View all queues</h3>
            <p class="mt-1 text-sm text-slate-500">See today's tickets across every service window.</p>
        </a>
        <a href="{{ route('admin.analytics.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-indigo-300 hover:shadow-md">
            <p class="text-xs font-bold uppercase tracking-widest text-indigo-700">Analytics</p>
            <h3 class="mt-2 font-bold">Queue history</h3>
            <p class="mt-1 text-sm text-slate-500">Compare window volume and find past tickets.</p>
        </a>
        <a href="{{ route('admin.missed-calls.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-amber-300 hover:shadow-md">
            <p class="text-xs font-bold uppercase tracking-widest text-amber-700">Exceptions</p>
            <h3 class="mt-2 font-bold">Review missed calls</h3>
            <p class="mt-1 text-sm text-slate-500">Check call history and queue pushbacks.</p>
        </a>
    </section>
@endsection
