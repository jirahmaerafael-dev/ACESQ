<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff sign in | ACESQ</title>
    @vite('resources/css/app.css')
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10 text-slate-900">
    <main class="w-full max-w-md rounded-2xl bg-white p-7 shadow-sm sm:p-9">
        <a href="{{ route('public.windows') }}" class="text-sm font-semibold text-blue-700">← ACESQ home</a>
        <h1 class="mt-6 text-2xl font-bold">Staff and admin sign in</h1>
        <p class="mt-2 text-sm text-slate-600">Use the username provided by the system administrator.</p>

        @if (session('status'))
            <p class="mt-5 rounded-lg bg-green-50 p-3 text-sm text-green-800">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <p role="alert" class="mt-5 rounded-lg bg-red-50 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </p>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" class="mt-6 space-y-5">
            @csrf
            <div>
                <label for="username" class="mb-2 block text-sm font-semibold">Username</label>
                <input id="username" name="username" value="{{ old('username') }}" required autofocus autocomplete="username"
                       class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-600 focus:outline-none">
            </div>
            <div>
                <label for="password" class="mb-2 block text-sm font-semibold">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       class="w-full rounded-lg border border-slate-300 px-4 py-3 focus:border-blue-600 focus:outline-none">
            </div>
            <button type="submit" class="w-full rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white hover:bg-blue-800">
                Sign in
            </button>
        </form>
    </main>
</body>
</html>
