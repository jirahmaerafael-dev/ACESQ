@extends('layouts.app')

@section('title', 'Staff accounts')
@section('page-title', 'Staff accounts')

@section('content')
    <section class="mb-7">
        <p class="text-xs font-bold uppercase tracking-widest text-blue-700">Access management</p>
        <h2 class="mt-1 text-2xl font-bold tracking-tight">Staff accounts</h2>
        <p class="mt-1 text-sm text-slate-600">Create accounts, assign service windows, and manage staff access.</p>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h3 class="text-lg font-bold">Create staff or admin account</h3>
        <form method="POST" action="{{ route('admin.staff.store') }}" class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @csrf
            <label class="text-sm font-semibold">Full name
                <input name="full_name" value="{{ old('full_name') }}" required maxlength="255" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            </label>
            <label class="text-sm font-semibold">Username
                <input name="username" value="{{ old('username') }}" required maxlength="100" autocomplete="off" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            </label>
            <label class="text-sm font-semibold">Role
                <select name="role" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
                    <option value="staff" @selected(old('role', 'staff') === 'staff')>Registrar staff</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                </select>
            </label>
            <label class="text-sm font-semibold">Assigned window (required for staff)
                <select name="window_id" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
                    <option value="">No window (admin only)</option>
                    @foreach ($windows as $window)
                        <option value="{{ $window->window_id }}" @selected((string) old('window_id') === (string) $window->window_id)>{{ $window->window_name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm font-semibold">Password
                <input type="password" name="password" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            </label>
            <label class="text-sm font-semibold">Confirm password
                <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            </label>
            <div class="sm:col-span-2 lg:col-span-3">
                <button class="rounded-xl bg-blue-700 px-5 py-3 font-semibold text-white transition hover:bg-blue-800">Create account</button>
            </div>
        </form>
    </section>

    <section class="mt-7 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h3 class="text-lg font-bold">Manage accounts</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $staff->count() }} account(s)</p>
            </div>
        </div>
        <div class="mt-5 space-y-4">
            @forelse ($staff as $member)
                <article class="rounded-2xl border border-slate-200 p-4 sm:p-5">
                    <form method="POST" action="{{ route('admin.staff.update', $member) }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        @csrf
                        @method('PATCH')
                        <label class="text-sm font-semibold">Full name
                            <input name="full_name" value="{{ $member->full_name }}" required maxlength="255" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
                        </label>
                        <label class="text-sm font-semibold">Username
                            <input name="username" value="{{ $member->username }}" required maxlength="100" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
                        </label>
                        <label class="text-sm font-semibold">Role
                            <select name="role" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
                                <option value="staff" @selected($member->role === 'staff')>Staff</option>
                                <option value="admin" @selected($member->role === 'admin')>Admin</option>
                            </select>
                        </label>
                        <label class="text-sm font-semibold">Window
                            <select name="window_id" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
                                <option value="">No window</option>
                                @foreach ($windows as $window)
                                    <option value="{{ $window->window_id }}" @selected($member->window_id === $window->window_id)>{{ $window->window_name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <div class="flex flex-wrap items-end gap-2 sm:col-span-2 lg:col-span-4">
                            <button class="rounded-xl border border-slate-300 px-4 py-2.5 font-semibold transition hover:bg-slate-50">Save account</button>
                        </div>
                    </form>

                    <div class="mt-4 flex flex-wrap items-end gap-3 border-t border-slate-100 pt-4">
                        <form method="POST" action="{{ route('admin.staff.password', $member) }}" class="flex flex-wrap items-end gap-2">
                            @csrf
                            <label class="text-xs font-semibold">New password
                                <input type="password" name="password" required minlength="8" autocomplete="new-password" class="mt-1 rounded-xl border border-slate-300 px-3 py-2">
                            </label>
                            <label class="text-xs font-semibold">Confirm password
                                <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="mt-1 rounded-xl border border-slate-300 px-3 py-2">
                            </label>
                            <button class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold transition hover:bg-slate-50">Reset password</button>
                        </form>
                        @if ($member->staff_id !== auth()->id())
                            <form method="POST" action="{{ route('admin.staff.delete', $member) }}" onsubmit="return confirm('Delete this account?')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-xl px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50">Delete account</button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-500">No staff or admin accounts have been created.</p>
            @endforelse
        </div>
    </section>
@endsection
