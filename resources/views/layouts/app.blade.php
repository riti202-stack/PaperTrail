<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PaperTrail</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <nav class="bg-white shadow p-4 flex justify-between items-center">
        <a href="{{ url('/dashboard') }}" class="font-semibold text-lg">PaperTrail</a>
        <div class="flex items-center gap-4 text-sm">
            @auth
                <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                <a href="{{ route('profile.edit') }}" class="text-gray-600">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-600">Logout</button>
                </form>

                @if(auth()->user()->role === 'admin')
    <a href="{{ route('admin.requests.index') }}" class="text-gray-600">Requests</a>
    <a href="{{ route('admin.runners.index') }}" class="text-gray-600">Runners</a>
@endif
            @endauth
        </div>
    </nav>

    @if (session('success'))
        <div class="max-w-3xl mx-auto mt-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <main class="py-6">
        @yield('content')
    </main>
</body>
</html>