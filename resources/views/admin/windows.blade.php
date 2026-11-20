@extends('layouts.app')

@section('title', 'Service windows')
@section('page-title', 'Service windows')

@section('content')
    <section class="mb-7">
        <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Configuration</p>
        <div class="mt-1 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Service windows</h2>
                <p class="mt-1 text-sm text-slate-600">Configure student-facing services and their ticket prefixes.</p>
            </div>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">{{ $windows->where('is_active', true)->count() }} open</span>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h3 class="text-lg font-bold">Add a service window</h3>
        <p class="mt-1 text-sm text-slate-500">Each prefix must be unique. For example, Registrar uses R and Window 1 uses W1.</p>
        <form method="POST" action="{{ route('admin.windows.store') }}" class="mt-5 flex flex-wrap items-end gap-3">
            @csrf
            <label class="min-w-56 flex-1 text-sm font-semibold">
                Window name
                <input name="window_name" value="{{ old('window_name') }}" required maxlength="255" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            </label>
            <label class="w-36 text-sm font-semibold">
                Ticket prefix
                <input name="queue_prefix" value="{{ old('queue_prefix') }}" required maxlength="8" pattern="[A-Za-z0-9]{1,8}" placeholder="e.g. R or W1" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5 uppercase">
            </label>
            <label class="flex items-center gap-2 py-2 text-sm">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 accent-blue-700">
                Open to students
            </label>
            <button class="rounded-xl bg-blue-700 px-5 py-2.5 font-semibold text-white transition hover:bg-blue-800">Add window</button>
        </form>
    </section>

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <h3 class="text-lg font-bold">Configured windows</h3>
            <p class="mt-1 text-sm text-slate-500">Window prefixes are saved on issued tickets so existing numbers remain unchanged.</p>
        </div>
        <div class="divide-y divide-slate-100 px-5">
            @forelse ($windows as $window)
                <div class="grid gap-3 py-5 lg:grid-cols-[1fr_auto] lg:items-center">
                    <form id="window-{{ $window->window_id }}" method="POST" action="{{ route('admin.windows.update', $window) }}" class="flex flex-wrap items-end gap-3">
                        @csrf
                        @method('PATCH')
                        <label class="min-w-48 flex-1 text-sm font-semibold">
                            Window name
                            <input name="window_name" value="{{ $window->window_name }}" required maxlength="255" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
                        </label>
                        <label class="w-36 text-sm font-semibold">
                            Prefix
                            <input name="queue_prefix" value="{{ $window->queue_prefix }}" required maxlength="8" pattern="[A-Za-z0-9]{1,8}" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5 uppercase">
                        </label>
                        <label class="flex items-center gap-2 py-2 text-sm">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked($window->is_active) class="h-4 w-4 accent-blue-700">
                            Open
                        </label>
                        <button class="rounded-xl border border-slate-300 px-4 py-2.5 font-semibold transition hover:bg-slate-50">Save changes</button>
                        <span class="text-xs text-slate-500">{{ $window->staff_users_count }} staff account(s)</span>
                    </form>
                    <form method="POST" action="{{ route('admin.windows.delete', $window) }}" onsubmit="return confirm('Delete this window? Only empty windows can be deleted.')">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-lg px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">Delete</button>
                    </form>
                </div>
            @empty
                <p class="py-6 text-sm text-slate-500">No windows configured.</p>
            @endforelse
        </div>
    </section>
@endsection
