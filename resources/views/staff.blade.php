@extends('layouts.app')

@section('title', 'Staff queue')
@section('page-title', $window->window_name.' dashboard')

@section('content')
    <div class="mb-7 flex flex-wrap items-end justify-between gap-4 rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 p-6 text-white shadow-xl shadow-slate-950/10 sm:p-8">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-300">Staff queue control</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight">{{ $window->window_name }}</h1>
            <p class="mt-1 text-sm text-slate-300">Manage tickets assigned to this service window.</p>
        </div>
        <form method="POST" action="{{ route('staff.call-next') }}">
            @csrf
            <button type="submit" class="rounded-xl bg-blue-500 px-6 py-3 font-bold text-white shadow-lg shadow-blue-950/20 transition hover:bg-blue-400">
                Call next student →
            </button>
        </form>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">Waiting in line</p>
            <p class="mt-2 text-3xl font-bold">{{ $waiting->count() }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">Now serving</p>
            <p class="mt-2 text-3xl font-bold tracking-wide">{{ $current ? $current->displayNumber() : '—' }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-500">Missed calls today</p>
            <p class="mt-2 text-3xl font-bold">{{ $missedCalls->count() }}</p>
        </div>
    </div>

    @if ($current)
        <section class="mt-7 rounded-2xl border border-blue-200 bg-blue-50 p-5 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-800">
                        {{ $current->status === 'called' ? 'Waiting for arrival' : 'Student present' }}
                    </p>
                    <p class="mt-1 text-4xl font-black tracking-tight text-blue-950">{{ $current->displayNumber() }}</p>
                    <p class="mt-1 font-semibold text-blue-900">{{ $current->student_name ?? 'Student' }}</p>
                    @if ($current->status === 'called' && $current->called_at)
                        <p class="mt-1 text-sm text-blue-800">
                            Grace period ends {{ $current->called_at->copy()->addMinutes(5)->format('g:i:s A') }}.
                            The student's page shows a live countdown.
                        </p>
                    @endif
                </div>
                <div class="flex gap-2">
                    @if ($current->status === 'called')
                        <form method="POST" action="{{ route('staff.present', $current) }}">
                            @csrf
                            <button class="rounded-lg bg-green-700 px-4 py-2 font-semibold text-white hover:bg-green-800">Mark present</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('staff.complete', $current) }}">
                            @csrf
                            <button class="rounded-lg bg-green-700 px-4 py-2 font-semibold text-white hover:bg-green-800">Mark completed</button>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Live queue</p>
            <h2 class="mt-1 text-xl font-bold">Waiting list</h2>
            <p class="mt-1 text-sm text-slate-500">Ticket numbers stay fixed; positions change when a ticket is pushed back.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="p-4">Position</th>
                        <th class="p-4">Ticket</th>
                        <th class="p-4">Student</th>
                        <th class="p-4">Missed calls</th>
                        <th class="p-4">Pushback history</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($waiting as $record)
                        <tr>
                            <td class="p-4 font-semibold">#{{ $record->queue_position }}</td>
                            <td class="p-4 font-bold tracking-wide">{{ $record->displayNumber() }}</td>
                            <td class="p-4">{{ $record->student_name ?? '—' }}</td>
                            <td class="p-4">{{ $record->missed_count }} / 3</td>
                            <td class="p-4">
                                @forelse ($record->missedCalls as $missedCall)
                                    <span class="mr-1 inline-block rounded bg-amber-50 px-2 py-1 text-xs text-amber-900">
                                        #{{ $missedCall->call_number }}: {{ $missedCall->slots_pushed === null ? 'forfeited' : '+'.$missedCall->slots_pushed.' slots' }}
                                    </span>
                                @empty
                                    <span class="text-slate-400">None</span>
                                @endforelse
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-8 text-center text-slate-500">No students are waiting.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <h2 class="text-xl font-bold">Missed-call history</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($missedCalls as $missedCall)
                <div class="flex flex-wrap justify-between gap-2 p-4 text-sm">
                    <span>
                        Ticket {{ $missedCall->queueRecord->displayNumber() }}
                        · {{ $missedCall->queueRecord->student_name ?? 'Student' }}
                        · call {{ $missedCall->call_number }}
                    </span>
                    <span class="text-slate-600">
                        {{ $missedCall->slots_pushed === null ? 'Number forfeited' : 'Pushed back '.$missedCall->slots_pushed.' slots' }}
                        · {{ $missedCall->missed_at->format('g:i A') }}
                    </span>
                </div>
            @empty
                <p class="p-5 text-sm text-slate-500">No missed calls recorded today.</p>
            @endforelse
        </div>
    </section>
@endsection
