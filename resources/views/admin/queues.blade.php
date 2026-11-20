@extends('layouts.app')

@section('title', 'Today’s queues')
@section('page-title', 'Today’s queues')

@section('content')
    <section class="mb-7">
        <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Live monitoring</p>
        <h2 class="mt-1 text-2xl font-bold tracking-tight">All windows · today’s queues</h2>
        <p class="mt-1 text-sm text-slate-600">Read-only monitoring across all service windows. Queue actions remain staff-only.</p>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-5">
            <div>
                <h3 class="text-lg font-bold">Queue tickets</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $records->count() }} ticket(s) issued today</p>
            </div>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-800">Updated {{ now()->format('g:i A') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="p-4">Window</th>
                        <th class="p-4">Student</th>
                        <th class="p-4">Ticket</th>
                        <th class="p-4">Position</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Misses</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($records as $record)
                        <tr>
                            <td class="p-4">{{ $record->window->window_name }}</td>
                            <td class="p-4">{{ $record->student_name ?? '—' }}</td>
                            <td class="p-4 font-bold tracking-wide">{{ $record->displayNumber() }}</td>
                            <td class="p-4">{{ $record->status === 'waiting' ? '#'.$record->queue_position : '—' }}</td>
                            <td class="p-4 capitalize">{{ $record->status }}</td>
                            <td class="p-4">{{ $record->missed_count }} / 3</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-slate-500">No queue records today.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
