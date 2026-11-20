@extends('layouts.app')

@section('title', 'Missed calls')
@section('page-title', 'Missed calls')

@section('content')
    <section class="mb-7">
        <p class="text-xs font-bold uppercase tracking-widest text-amber-700">Queue exceptions</p>
        <h2 class="mt-1 text-2xl font-bold tracking-tight">Today’s missed-call history</h2>
        <p class="mt-1 text-sm text-slate-600">Review missed calls, pushbacks, and forfeited tickets across all service windows.</p>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-end justify-between gap-3 border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-lg font-bold">Missed calls</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $missedCalls->count() }} event(s) recorded today</p>
            </div>
            <span class="text-xs font-semibold text-slate-500">{{ today()->format('F j, Y') }}</span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($missedCalls as $missedCall)
                <div class="flex flex-wrap items-center justify-between gap-3 py-4">
                    <div>
                        <p class="font-semibold">{{ $missedCall->queueRecord->window->window_name }} · {{ $missedCall->queueRecord->displayNumber() }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $missedCall->queueRecord->student_name ?? 'Student name unavailable' }} · Call {{ $missedCall->call_number }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-sm font-semibold {{ $missedCall->slots_pushed === null ? 'text-red-700' : 'text-amber-800' }}">
                            {{ $missedCall->slots_pushed === null ? 'Ticket forfeited' : 'Pushed back '.$missedCall->slots_pushed.' slots' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">{{ $missedCall->missed_at->format('g:i A') }}</p>
                    </div>
                </div>
            @empty
                <p class="py-8 text-center text-sm text-slate-500">No missed calls have been recorded today.</p>
            @endforelse
        </div>
    </section>
@endsection
