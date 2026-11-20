<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | ACESQ</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    @if (auth()->user()->isAdmin())
        <div class="min-h-screen md:flex">
            <aside id="admin-queue-sidebar" class="border-b border-slate-200 bg-white md:sticky md:top-0 md:flex md:h-screen md:w-64 md:shrink-0 md:flex-col md:border-b-0 md:border-r">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 border-b border-slate-100 px-5 py-5">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-blue-700 text-sm font-black tracking-wide text-white">AQ</span>
                    <span>
                        <span class="block text-base font-bold tracking-tight">ACESQ</span>
                        <span class="block text-xs text-slate-500">School Queue Admin</span>
                    </span>
                </a>

                <nav aria-label="Admin navigation" class="flex gap-2 overflow-x-auto px-3 py-4 md:flex-1 md:flex-col md:gap-6 md:overflow-y-auto">
                    <div class="flex gap-2 md:flex-col">
                        <p class="hidden px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 md:block">Workspace</p>
                        <a href="{{ route('admin.dashboard') }}" @class([
                            'flex shrink-0 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition',
                            'bg-slate-100 text-slate-900' => request()->routeIs('admin.dashboard'),
                            'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! request()->routeIs('admin.dashboard'),
                        ])>
                            <svg class="h-5 w-5 shrink-0 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 10.5 8.25-6.75 8.25 6.75v9a.75.75 0 0 1-.75.75h-5.25v-6h-4.5v6H4.5a.75.75 0 0 1-.75-.75v-9Z" />
                            </svg>
                            Dashboard
                        </a>
                    </div>
                    <div class="flex gap-2 md:flex-col">
                        <p class="hidden px-3 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 md:block">Manage</p>
                        <a href="{{ route('admin.windows.index') }}" @class([
                            'flex shrink-0 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                            'bg-slate-100 text-slate-900' => request()->routeIs('admin.windows.*'),
                            'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! request()->routeIs('admin.windows.*'),
                        ])>
                            <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 20.25h16.5M5.25 20.25V5.625A1.875 1.875 0 0 1 7.125 3.75h9.75a1.875 1.875 0 0 1 1.875 1.875V20.25M9 7.5h.008v.008H9V7.5Zm0 3.75h.008v.008H9v-.008Zm0 3.75h.008v.008H9V15Zm6-7.5h.008v.008H15V7.5Zm0 3.75h.008v.008H15v-.008Zm0 3.75h.008v.008H15V15Z" />
                            </svg>
                            Service windows
                        </a>
                        <a href="{{ route('admin.staff.index') }}" @class([
                            'flex shrink-0 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                            'bg-slate-100 text-slate-900' => request()->routeIs('admin.staff.*'),
                            'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! request()->routeIs('admin.staff.*'),
                        ])>
                            <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5v-1.875A3.375 3.375 0 0 0 12.375 14.25h-4.5A3.375 3.375 0 0 0 4.5 17.625V19.5m13.5 0h2.25v-1.875a3.375 3.375 0 0 0-2.625-3.29M10.125 10.5a3.375 3.375 0 1 0 0-6.75 3.375 3.375 0 0 0 0 6.75Zm7.125-.75a3 3 0 1 0-1.125-5.781" />
                            </svg>
                            Staff accounts
                        </a>
                        <a href="{{ route('admin.queues.index') }}" @class([
                            'flex shrink-0 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                            'bg-slate-100 text-slate-900' => request()->routeIs('admin.queues.*'),
                            'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! request()->routeIs('admin.queues.*'),
                        ])>
                            <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12m-12 5.25h12m-12 5.25h12M3.75 6.75h.008v.008H3.75V6.75Zm0 5.25h.008v.008H3.75V12Zm0 5.25h.008v.008H3.75v-.008Z" />
                            </svg>
                            Today's queues
                        </a>
                        <a href="{{ route('admin.analytics.index') }}" @class([
                            'flex shrink-0 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                            'bg-slate-100 text-slate-900' => request()->routeIs('admin.analytics.*'),
                            'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! request()->routeIs('admin.analytics.*'),
                        ])>
                            <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5V12m5 7.5V8.25m5 11.25v-5.25m5 5.25V4.5M3 19.5h18" />
                            </svg>
                            Queue history &amp; analytics
                        </a>
                        <a href="{{ route('admin.missed-calls.index') }}" @class([
                            'flex shrink-0 items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                            'bg-slate-100 text-slate-900' => request()->routeIs('admin.missed-calls.*'),
                            'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! request()->routeIs('admin.missed-calls.*'),
                        ])>
                            <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v4.5l3 1.5m5.25-2.25a8.25 8.25 0 1 1-16.5 0 8.25 8.25 0 0 1 16.5 0Z" />
                            </svg>
                            Missed calls
                        </a>
                    </div>
                </nav>

                <div class="hidden border-t border-slate-100 p-4 md:block">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-blue-100 text-sm font-bold text-blue-800">AD</span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-semibold">{{ auth()->user()->full_name }}</span>
                            <span class="block text-xs text-slate-500">Administrator</span>
                        </span>
                        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Are you sure you want to sign out?')">
                            @csrf
                            <button aria-label="Sign out" title="Sign out" class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 8.25V5.625A1.875 1.875 0 0 0 13.875 3.75h-7.5A1.875 1.875 0 0 0 4.5 5.625v12.75a1.875 1.875 0 0 0 1.875 1.875h7.5a1.875 1.875 0 0 0 1.875-1.875V15.75m-3.75-3.75h12m0 0-3.75-3.75M20.25 12l-3.75 3.75" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="min-w-0 flex-1">
                <header class="flex min-h-16 flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-white px-5 py-3 sm:px-8">
                    <div>
                        <p class="text-xs font-medium text-slate-500">ACESQ / Administration</p>
                        <h1 class="text-sm font-bold">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="hidden text-sm text-slate-500 sm:block">{{ now()->format('D, M j') }}</span>
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-blue-100 text-xs font-bold text-blue-800">AD</span>
                        <span class="hidden text-sm font-semibold sm:block">{{ auth()->user()->full_name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="md:hidden" onsubmit="return confirm('Are you sure you want to sign out?')">
                            @csrf
                            <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">Sign out</button>
                        </form>
                    </div>
                </header>

                <main class="mx-auto w-full max-w-[90rem] px-4 py-6 sm:px-8 lg:py-8">
                    @if (session('success'))
                        <p role="status" class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">{{ session('success') }}</p>
                    @endif
                    @if (session('error'))
                        <p role="alert" class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-medium text-amber-900">{{ session('error') }}</p>
                    @endif
                    @if ($errors->any())
                        <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                            <ul class="list-inside list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <header class="bg-slate-950 text-white shadow-lg shadow-slate-950/10">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-4 sm:px-8">
                <div class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-blue-500 text-sm font-black tracking-wide">AQ</span>
                    <div>
                        <a href="{{ route('staff.dashboard') }}" class="text-xl font-bold tracking-tight">ACESQ</a>
                        <p class="text-sm text-slate-300">@yield('page-title', 'Queue management')</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="hidden text-right text-sm sm:block">
                        <span class="block font-semibold">{{ auth()->user()->full_name }}</span>
                        <span class="text-slate-300">{{ auth()->user()->window?->window_name }}</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Are you sure you want to sign out?')">
                        @csrf
                        <button class="rounded-xl border border-slate-700 px-4 py-2 text-sm font-semibold transition hover:border-slate-500 hover:bg-slate-800">Sign out</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-5 py-8 sm:px-8">
            @if (session('success'))
                <p role="status" class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">{{ session('success') }}</p>
            @endif
            @if (session('error'))
                <p role="alert" class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-medium text-amber-900">{{ session('error') }}</p>
            @endif
            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    @endif
</body>
</html>
